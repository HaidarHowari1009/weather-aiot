<?php
$dbPath = __DIR__ . '/../database/weather.sqlite';

date_default_timezone_set('Asia/Jakarta');

try {
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Auto-create tabel jika belum ada
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS weather_data (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            observation_time DATETIME NOT NULL,
            temperature REAL NOT NULL,
            humidity REAL NOT NULL,
            wind_speed REAL NOT NULL,
            cloud_cover REAL NOT NULL,
            weather_desc TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT (datetime('now', 'localtime'))
        );
        
        CREATE TABLE IF NOT EXISTS prediction_history (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            temperature REAL NOT NULL,
            humidity REAL NOT NULL,
            wind_speed REAL NOT NULL,
            cloud_cover REAL NOT NULL,
            prediction_result TEXT NOT NULL,
            confidence REAL NOT NULL,
            created_at TIMESTAMP DEFAULT (datetime('now', 'localtime'))
        );
        
        CREATE TABLE IF NOT EXISTS model_evaluation (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            accuracy REAL NOT NULL,
            precision_score REAL NOT NULL,
            recall_score REAL NOT NULL,
            f1_score REAL NOT NULL,
            created_at TIMESTAMP DEFAULT (datetime('now', 'localtime'))
        );
    ");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
