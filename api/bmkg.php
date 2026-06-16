<?php
require_once '../config/database.php';

// Function to map weather code to description
function getWeatherDesc(int|string $code): string {
    $weather_codes = [
        0 => "Cerah",
        1 => "Cerah Berawan",
        2 => "Cerah Berawan",
        3 => "Berawan",
        4 => "Berawan Tebal",
        5 => "Udara Kabur",
        10 => "Asap",
        45 => "Kabut",
        60 => "Hujan Ringan",
        61 => "Hujan Sedang",
        63 => "Hujan Lebat",
        80 => "Hujan Lokal",
        95 => "Hujan Petir",
        97 => "Hujan Petir"
    ];
    return isset($weather_codes[(int)$code]) ? $weather_codes[(int)$code] : "Tidak Diketahui";
}

// Function to estimate cloud cover based on weather code
function estimateCloudCover(int|string $code): int {
    if (in_array((int)$code, [0])) return rand(0, 10);
    if (in_array((int)$code, [1, 2])) return rand(10, 40);
    if (in_array((int)$code, [3])) return rand(40, 70);
    if (in_array((int)$code, [4])) return rand(70, 100);
    return rand(80, 100); // For rain/fog etc.
}

try {
    // URL BMKG for DKI Jakarta (or change to other provinces)
    $url = "https://data.bmkg.go.id/DataMKG/MEWS/DigitalForecast/DigitalForecast-DKIJakarta.xml";
    
    $xmlString = @file_get_contents($url);
    if ($xmlString === FALSE) {
        throw new Exception("Failed to fetch XML from BMKG.");
    }
    
    $xml = simplexml_load_string($xmlString);
    if ($xml === FALSE) {
        throw new Exception("Failed to parse XML.");
    }

    // Ambil data untuk satu area (contoh: Jakarta Pusat)
    $areaId = "501233"; // Jakarta Pusat
    $targetArea = null;
    
    foreach ($xml->forecast->area as $area) {
        if ((string)$area['id'] == $areaId) {
            $targetArea = $area;
            break;
        }
    }
    
    if (!$targetArea) {
        throw new Exception("Area not found in XML.");
    }
    
    // Parse Parameters
    $weatherData = [];
    foreach ($targetArea->parameter as $param) {
        $id = (string)$param['id'];
        if (in_array($id, ['t', 'hu', 'ws', 'weather'])) {
            foreach ($param->timerange as $tr) {
                $datetime = (string)$tr['datetime'];
                if (!isset($weatherData[$datetime])) {
                    $weatherData[$datetime] = [];
                }
                $weatherData[$datetime][$id] = (string)$tr->value[0];
            }
        }
    }
    
    // Find the closest forecast to CURRENT TIME (or simply insert all future forecasts as historical simulation)
    // To populate our database, we will insert all fetched times if they don't exist yet.
    
    $insertedCount = 0;
    foreach ($weatherData as $dt => $data) {
        if (isset($data['t']) && isset($data['hu']) && isset($data['ws']) && isset($data['weather'])) {
            // Format datetime from YYYYMMDDHHMM to YYYY-MM-DD HH:MM:SS
            $obs_time = substr($dt, 0, 4) . '-' . substr($dt, 4, 2) . '-' . substr($dt, 6, 2) . ' ' . substr($dt, 8, 2) . ':' . substr($dt, 10, 2) . ':00';
            
            $temp = (float)$data['t'];
            $humidity = (float)$data['hu'];
            $wind = (float)$data['ws']; // In knots
            $wind_kmh = $wind * 1.852; // Convert knots to km/h
            $weather_code = $data['weather'];
            
            $desc = getWeatherDesc($weather_code);
            $cloud_cover = estimateCloudCover($weather_code);
            
            // Check if already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM weather_data WHERE observation_time = ?");
            $stmt->execute([$obs_time]);
            if ($stmt->fetchColumn() == 0) {
                $insert = $pdo->prepare("INSERT INTO weather_data (observation_time, temperature, humidity, wind_speed, cloud_cover, weather_desc) VALUES (?, ?, ?, ?, ?, ?)");
                $insert->execute([$obs_time, $temp, $humidity, $wind_kmh, $cloud_cover, $desc]);
                $insertedCount++;
            }
        }
    }
    
    echo json_encode(["status" => "success", "message" => "BMKG Data fetched successfully. $insertedCount new records inserted."]);

} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
