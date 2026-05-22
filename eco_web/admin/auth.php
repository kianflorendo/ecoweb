<?php

session_start();

if (empty($_SESSION['bb_admin'])) {
    header('Location: login.php'); exit;
}

// Auto-logout after 2 hours of inactivity
if (isset($_SESSION['bb_admin_time']) && (time() - $_SESSION['bb_admin_time']) > 7200) {
    session_destroy();
    header('Location: login.php?timeout=1'); exit;
}
$_SESSION['bb_admin_time'] = time();

// ── DB connection helper ──
define('DB_HOST','localhost');
define('DB_USER','root');
define('DB_PASS','');
define('DB_NAME','bottleback');

function bb_db(): ?mysqli {
    static $conn = null;
    if ($conn) return $conn;
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) { $conn = null; return null; }
        return $conn;
    } catch (Exception $e) { return null; }
}

function db_connected(): bool { return bb_db() !== null; }
