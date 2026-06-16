<?php
require_once '../config/database.php';

// Generate 500 records of dummy data for the last year to train the model
$total_records = 500;

$weather_conditions = ["Cerah", "Cerah Berawan", "Berawan", "Berawan Tebal", "Hujan Ringan", "Hujan Sedang", "Hujan Lebat"];

$inserted = 0;
for ($i = 0; $i < $total_records; $i++) {
    // Random date within the last 365 days
    $timestamp = time() - rand(0, 365 * 24 * 60 * 60);
    $obs_time = date('Y-m-d H:i:s', $timestamp);
    
    // Generate realistic correlation
    // Cerah -> high temp, low humidity, low cloud
    // Hujan -> low temp, high humidity, high cloud
    $condition = $weather_conditions[array_rand($weather_conditions)];
    
    if (strpos($condition, 'Cerah') !== false) {
        $temp = rand(280, 350) / 10; // 28.0 - 35.0
        $humidity = rand(50, 75);
        $wind = rand(5, 20);
        $cloud = rand(0, 30);
    } elseif (strpos($condition, 'Berawan') !== false) {
        $temp = rand(260, 310) / 10; // 26.0 - 31.0
        $humidity = rand(65, 85);
        $wind = rand(10, 25);
        $cloud = rand(40, 80);
    } else { // Hujan
        $temp = rand(230, 280) / 10; // 23.0 - 28.0
        $humidity = rand(80, 100);
        $wind = rand(15, 35);
        $cloud = rand(80, 100);
    }
    
    $insert = $pdo->prepare("INSERT INTO weather_data (observation_time, temperature, humidity, wind_speed, cloud_cover, weather_desc) VALUES (?, ?, ?, ?, ?, ?)");
    $insert->execute([$obs_time, $temp, $humidity, $wind, $cloud, $condition]);
    $inserted++;
}

echo "Successfully seeded $inserted dummy records for training.";
?>
