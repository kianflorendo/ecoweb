// ============================================================
//  BottleBack — ESP32 Firmware
//  Bottle detection : HC-SR04 ultrasonic #1 (<5 cm = bottle)
//  Bin level        : HC-SR04 ultrasonic #2
//  LCD              : initialized once in setup()
//  Time             : NTP Philippine Time (UTC+8)
// ============================================================

#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <ESP32Servo.h>
#include <time.h>

const char* WIFI_SSID     = "Cream";
const char* WIFI_PASSWORD = "hatdog123";
const char* SERVER_URL    = "http://10.250.209.92:8000/api/receive-data";
const char* PING_URL      = "http://10.250.209.92:8000/api/machine/ping";
const char* NODE_ID       = "node_001";

// ── Bottle detection HC-SR04 (points at bottle slot) ─────────
#define BOTTLE_TRIG     18
#define BOTTLE_ECHO     19

// ── Bin level HC-SR04 (mounted top of bin, points down) ──────
#define BIN_TRIG        26
#define BIN_ECHO        27

#define SERVO_PIN       13
#define BUZZER_PIN      25
#define LED_GREEN       14
#define LED_RED         32

#define SERVO_CLOSED    0
#define SERVO_OPEN      90
#define BOTTLE_MAX_CM   15.0f   // <15 cm = bottle detected
#define GATE_OPEN_MS    1500
#define LOG_DELAY_MS    3000
#define COOLDOWN_MS     1000
#define BIN_PING_MS     5000
#define BIN_EMPTY_CM    120

LiquidCrystal_I2C lcd(0x27, 16, 2);
Servo gateServo;

int           currentBinLevel = 0;
unsigned long lastBinPing     = 0;

// ── LCD: init once in setup(), clear before each write ───────
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
  pinMode(BUZZER_PIN,  OUTPUT);
  pinMode(LED_GREEN,   OUTPUT);
  pinMode(LED_RED,     OUTPUT);

  digitalWrite(BUZZER_PIN, LOW);
  digitalWrite(LED_GREEN,  LOW);
  digitalWrite(LED_RED,    LOW);

  gateServo.attach(SERVO_PIN);
  gateServo.write(SERVO_CLOSED);
  delay(300);

  // LCD — initialize once only
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
  Serial.println("Ready. Ultrasonic bottle detection (<5cm).");
}

// ─────────────────────────────────────────────────────────────
void loop() {

  // Debug: print bottle distance and bin level every second
  static unsigned long lastDebug = 0;
  if (millis() - lastDebug >= 1000) {
    lastDebug = millis();
    float bd = measureBottleDistance();
    Serial.printf("BOTTLE=%.1fcm(%s)  BIN=%d%%\n",
      bd, bd < BOTTLE_MAX_CM ? "DETECTED" : "clear", currentBinLevel);
  }

  if (bottleConfirmed()) {

    // Step 1 — Detecting
    lcdShow("Detecting...", "Please wait...");
    Serial.println("Bottle confirmed");
    delay(LOG_DELAY_MS);

    // Step 2 — Re-confirm bottle still present
    if (bottleConfirmed()) {

      // Step 3 — Accepted
      lcdShow(">> ACCEPTED! <<", "Opening gate...");
      digitalWrite(LED_GREEN, HIGH);
      beep(1, 200);
      Serial.println(">> ACCEPTED");

      gateServo.write(SERVO_OPEN);
      delay(GATE_OPEN_MS);
      gateServo.write(SERVO_CLOSED);

      // Step 4 — Reward dispensed
      lcdShow("Reward", "Dispensed!  :)");
      delay(1500);

      // Step 5 — Thank you
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

      // Wait for bottle to be removed (up to 6 s)
      unsigned long t = millis() + 6000;
      while (measureBottleDistance() < BOTTLE_MAX_CM && millis() < t) delay(100);
      Serial.println("Cleared — ready");

    } else {
      Serial.println("Removed early — cancelled");
    }

    delay(COOLDOWN_MS);
    lcdIdle();
  }

  // Periodic bin level ping to server
  if (millis() - lastBinPing >= BIN_PING_MS) {
    lastBinPing     = millis();
    currentBinLevel = measureBinLevel();
    Serial.printf("Bin ping: %d%%\n", currentBinLevel);
    pingBinLevel();
  }
}

// ─────────────────────────────────────────────────────────────
// Single HC-SR04 reading (generic)
float readUltrasonic(int trig, int echo) {
  digitalWrite(trig, LOW);  delayMicroseconds(4);
  digitalWrite(trig, HIGH); delayMicroseconds(10);
  digitalWrite(trig, LOW);
  long d = pulseIn(echo, HIGH, 30000);   // 30 ms timeout ≈ 5 m
  if (d == 0) return 999.0f;
  return d * 0.01715f;                   // microseconds → cm
}

// Bottle sensor: average 3 readings
float measureBottleDistance() {
  float sum = 0; int v = 0;
  for (int i = 0; i < 3; i++) {
    float cm = readUltrasonic(BOTTLE_TRIG, BOTTLE_ECHO);
    if (cm < 400) { sum += cm; v++; }
    delay(10);
  }
  return v == 0 ? 999.0f : sum / v;
}

// Bin sensor: average 5 readings
float measureDistance() {
  float sum = 0; int v = 0;
  for (int i = 0; i < 5; i++) {
    float cm = readUltrasonic(BIN_TRIG, BIN_ECHO);
    if (cm < 400) { sum += cm; v++; }
    delay(15);
  }
  return v == 0 ? 999.0f : sum / v;
}

// 6 readings × 50 ms — all must be <5 cm to confirm a bottle
bool bottleConfirmed() {
  for (int i = 0; i < 6; i++) {
    if (measureBottleDistance() >= BOTTLE_MAX_CM) return false;
    delay(50);
  }
  return true;
}

int measureBinLevel() {
  float dist = measureDistance();
  if (dist >= BIN_EMPTY_CM) return 0;
  int lv = dist > 20 ? (int)((BIN_EMPTY_CM - dist) * 0.8f)
                     : 80 + (int)(20 - dist);
  return constrain(lv, 0, 100);
}

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
