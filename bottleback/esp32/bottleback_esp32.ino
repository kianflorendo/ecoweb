// ============================================================
//  BottleBack — ESP32 Firmware
//  Trigger 1 : Inductive sensor → immediate metal rejection
//  Trigger 2 : HC-SR04 ultrasonic → bottle detection
//  Validation : Capacitive sensor → confirm plastic
//  Bin level  : HC-SR04 ultrasonic #2
//  LCD        : initialized once in setup()
//  Time       : NTP Philippine Time (UTC+8)
// ============================================================

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ESP32Servo.h>
#include <time.h>

const char* WIFI_SSID     = "Cream";
const char* WIFI_PASSWORD = "hatdog123";
const char* SERVER_URL    = "http://10.63.128.92:8000/api/receive-data";
const char* PING_URL      = "http://10.63.128.92:8000/api/machine/ping";
const char* NODE_ID       = "node_001";

// ── Bottle detection HC-SR04 ──────────────────────────────────
#define BOTTLE_TRIG     18
#define BOTTLE_ECHO     19

// ── Bin level HC-SR04 ────────────────────────────────────────
#define BIN_TRIG        26
#define BIN_ECHO        27

// ── Sensors via PC817 (LOW = triggered) ──────────────────────
#define CAP_PIN         33   // Capacitive — detects plastic
#define IND_PIN         35   // Inductive  — detects metal (PRIMARY TRIGGER)

#define SERVO_PIN       13
#define BUZZER_PIN      25
#define LED_GREEN       14
#define LED_RED         32

#define SERVO_CLOSED    0
#define SERVO_OPEN      90
#define BOTTLE_MAX_CM   15.0f
#define GATE_OPEN_MS    1500
#define LOG_DELAY_MS    2000
#define COOLDOWN_MS     1500
#define BIN_PING_MS     5000
#define BIN_EMPTY_CM    120
#define BIN_MAX_CM      140

LiquidCrystal_I2C lcd(0x27, 16, 2);
Servo gateServo;

int           currentBinLevel = 0;
unsigned long lastBinPing     = 0;

// ── LCD ───────────────────────────────────────────────────────
void lcdShow(const char* r0, const char* r1) {
  lcd.clear();
  delay(3);
  lcd.setCursor(0, 0); lcd.print(r0);
  lcd.setCursor(0, 1); lcd.print(r1);
}

void lcdIdle() { lcdShow("Insert Bottle", "to Begin..."); }

// ── NTP Philippine Time (UTC+8) ───────────────────────────────
void syncTime() {
  configTime(8 * 3600, 0, "pool.ntp.org", "time.nist.gov");
  struct tm ti;
  int tries = 0;
  while (!getLocalTime(&ti) && tries < 20) { delay(500); tries++; }
  if (tries < 20)
    Serial.printf("PHT: %04d-%02d-%02d %02d:%02d:%02d\n",
      ti.tm_year+1900, ti.tm_mon+1, ti.tm_mday,
      ti.tm_hour, ti.tm_min, ti.tm_sec);
  else
    Serial.println("NTP sync failed");
}

// ─────────────────────────────────────────────────────────────
void setup() {
  Serial.begin(115200);
  delay(200);

  pinMode(BOTTLE_TRIG, OUTPUT);
  pinMode(BOTTLE_ECHO, INPUT);
  pinMode(BIN_TRIG,    OUTPUT);
  pinMode(BIN_ECHO,    INPUT);
  pinMode(CAP_PIN,     INPUT);
  pinMode(IND_PIN,     INPUT);
  pinMode(BUZZER_PIN,  OUTPUT);
  pinMode(LED_GREEN,   OUTPUT);
  pinMode(LED_RED,     OUTPUT);

  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_GREEN,  LOW);
  digitalWrite(LED_RED,    LOW);

  gateServo.attach(SERVO_PIN);
  gateServo.write(SERVO_CLOSED);
  delay(300);

  Wire.begin(21, 22, 100000UL);
  delay(100);
  lcd.init();
  delay(100);
  lcd.backlight();
  delay(50);

  lcdShow("BottleBack", "Starting...");
  delay(1200);

  connectWiFi();
  syncTime();

  currentBinLevel = measureBinLevel();
  lcdIdle();
  Serial.println("Ready.");
}

// ─────────────────────────────────────────────────────────────
void loop() {

  // Debug: all sensors every second
  static unsigned long lastDebug = 0;
  if (millis() - lastDebug >= 1000) {
    lastDebug = millis();
    float bd      = measureBottleDistance();
    float binDist = measureBinDistance();
    currentBinLevel = binDistToLevel(binDist);
    bool cap = readCapacitive();
    bool ind = readInductive();
    Serial.printf("BOTTLE=%.1fcm(%s)  CAP=%s  IND=%s  BIN=%d%%  BIN_DIST=%.1fcm\n",
      bd,
      bd < BOTTLE_MAX_CM ? "DETECTED" : "clear",
      cap ? "PLASTIC" : "none",
      ind ? "METAL"   : "none",
      currentBinLevel, binDist);
  }

  // ── PRIORITY 1: Inductive sensor → immediate metal rejection ─
  if (metalConfirmed()) {
    Serial.println(">> METAL DETECTED — rejecting immediately");
    lcdShow("!! METAL !!", "Not Accepted");
    digitalWrite(LED_RED, HIGH);
    beep(3, 100);
    delay(800);

    lcdShow(">> REJECTED <<", "Metal Detected");
    delay(2000);

    digitalWrite(LED_RED, LOW);
    sendToServer("Rejected", 1, 0);

    // Wait for metal object to be removed before resuming
    Serial.println("Waiting for object removal...");
    while (readInductive()) delay(100);
    Serial.println("Object removed — ready");

    delay(COOLDOWN_MS);
    lcdIdle();
    return;
  }

  // ── PRIORITY 2: Ultrasonic → bottle slot detection ───────────
  if (bottleConfirmed()) {
    lcdShow("Detecting...", "Please wait...");
    Serial.println("Bottle confirmed — validating...");
    delay(LOG_DELAY_MS);

    if (bottleConfirmed()) {
      bool isPlastic = readCapacitive();
      bool isMetal   = readInductive();   // final check
      bool accepted  = isPlastic && !isMetal;

      Serial.printf("CAP=%s  IND=%s  → %s\n",
        isPlastic ? "PLASTIC"     : "NOT PLASTIC",
        isMetal   ? "METAL"       : "no metal",
        accepted  ? "ACCEPT"      : "REJECT");

      if (accepted) {
        // ── ACCEPT ─────────────────────────────────────────────
        lcdShow(">> ACCEPTED! <<", "Opening gate...");
        digitalWrite(LED_GREEN, HIGH);
        beep(1, 200);
        Serial.println(">> ACCEPTED");

        gateServo.write(SERVO_OPEN);
        delay(GATE_OPEN_MS);
        gateServo.write(SERVO_CLOSED);

        lcdShow("Reward", "Dispensed!  :)");
        delay(1500);
        lcdShow("Thank You!", "Keep Recycling!");
        beep(2, 100);
        delay(2000);

        digitalWrite(LED_GREEN, LOW);
        sendToServer("Accepted", 1, 1);

        currentBinLevel = measureBinLevel();
        if (currentBinLevel >= 90) {
          lcdShow("Bin FULL!", "Please empty");
          beep(3, 150);
          delay(2000);
        }

        unsigned long t = millis() + 6000;
        while (measureBottleDistance() < BOTTLE_MAX_CM && millis() < t) delay(100);
        Serial.println("Cleared — ready");

      } else {
        // ── REJECT (not plastic or metal slipped through) ───────
        const char* reason = isMetal ? "Metal Detected" : "Not Plastic";
        lcdShow(">> REJECTED <<", reason);
        digitalWrite(LED_RED, HIGH);
        beep(2, 150);
        Serial.println(String(">> REJECTED: ") + reason);
        delay(2500);
        digitalWrite(LED_RED, LOW);
        sendToServer("Rejected", 1, 0);
      }

    } else {
      Serial.println("Removed early — cancelled");
    }

    delay(COOLDOWN_MS);
    lcdIdle();
  }

  // ── Periodic bin ping ─────────────────────────────────────────
  if (millis() - lastBinPing >= BIN_PING_MS) {
    lastBinPing     = millis();
    currentBinLevel = measureBinLevel();
    Serial.printf("Bin ping: %d%%\n", currentBinLevel);
    pingBinLevel();
  }
}

// ─────────────────────────────────────────────────────────────
//  SENSOR READS
// ─────────────────────────────────────────────────────────────

bool readCapacitive() { return digitalRead(CAP_PIN) == LOW; }
bool readInductive()  { return digitalRead(IND_PIN) == LOW; }

// 3 consecutive readings must all show metal — prevents false triggers
bool metalConfirmed() {
  for (int i = 0; i < 3; i++) {
    if (!readInductive()) return false;
    delay(30);
  }
  return true;
}

float readUltrasonic(int trig, int echo) {
  digitalWrite(trig, LOW);  delayMicroseconds(4);
  digitalWrite(trig, HIGH); delayMicroseconds(10);
  digitalWrite(trig, LOW);
  long d = pulseIn(echo, HIGH, 30000);
  if (d == 0) return 999.0f;
  return d * 0.01715f;
}

float measureBottleDistance() {
  float sum = 0; int v = 0;
  for (int i = 0; i < 3; i++) {
    float cm = readUltrasonic(BOTTLE_TRIG, BOTTLE_ECHO);
    if (cm < 400) { sum += cm; v++; }
    delay(10);
  }
  return v == 0 ? 999.0f : sum / v;
}

float measureBinDistance() {
  float buf[5];
  int n = 0;
  for (int i = 0; i < 9 && n < 5; i++) {
    float cm = readUltrasonic(BIN_TRIG, BIN_ECHO);
    if (cm > 2.0f && cm < BIN_MAX_CM) buf[n++] = cm;
    delay(30);
  }
  if (n == 0) return (float)BIN_EMPTY_CM;
  for (int i = 1; i < n; i++) {
    float k = buf[i]; int j = i - 1;
    while (j >= 0 && buf[j] > k) { buf[j+1] = buf[j]; j--; }
    buf[j+1] = k;
  }
  return buf[n / 2];
}

bool bottleConfirmed() {
  for (int i = 0; i < 6; i++) {
    if (measureBottleDistance() >= BOTTLE_MAX_CM) return false;
    delay(50);
  }
  return true;
}

int binDistToLevel(float dist) {
  if (dist >= BIN_EMPTY_CM) return 0;
  int lv = dist > 20 ? (int)((BIN_EMPTY_CM - dist) * 0.8f)
                     : 80 + (int)(20 - dist);
  return constrain(lv, 0, 100);
}

int measureBinLevel() {
  return binDistToLevel(measureBinDistance());
}

// ─────────────────────────────────────────────────────────────
//  NETWORK
// ─────────────────────────────────────────────────────────────

void sendToServer(String status, int b, int r) {
  if (WiFi.status() != WL_CONNECTED) connectWiFi();
  HTTPClient http;
  http.begin(SERVER_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  String body = "bottle_count="   + String(b)               +
                "&reward_amount=" + String(r)               +
                "&status="        + status                  +
                "&bin_level="     + String(currentBinLevel) +
                "&node_id="       + String(NODE_ID);
  Serial.println("POST: " + body);
  int code = http.POST(body);
  Serial.printf("HTTP: %d\n", code);
  http.end();
}

void pingBinLevel() {
  if (WiFi.status() != WL_CONNECTED) return;
  HTTPClient http;
  http.begin(PING_URL);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");
  http.POST("node_id=" + String(NODE_ID) + "&bin_level=" + String(currentBinLevel));
  http.end();
}

void connectWiFi() {
  Serial.print("Connecting to "); Serial.println(WIFI_SSID);
  lcdShow("Connecting...", WIFI_SSID);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  int t = 0;
  while (WiFi.status() != WL_CONNECTED && t < 20) {
    delay(500); Serial.print("."); t++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    String ip = WiFi.localIP().toString();
    Serial.println("\nConnected: " + ip);
    lcdShow("WiFi OK!", ip.c_str());
  } else {
    Serial.println("\nFailed");
    lcdShow("WiFi Failed", "Offline Mode");
  }
  delay(1200);
}

void beep(int n, int ms) {
  for (int i = 0; i < n; i++) {
    digitalWrite(BUZZER_PIN, HIGH); delay(ms);
    digitalWrite(BUZZER_PIN, LOW);
    if (i < n - 1) delay(100);
  }
}
