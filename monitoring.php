<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch the latest weather data
$stmt = $pdo->query("SELECT * FROM weather_data ORDER BY observation_time DESC LIMIT 1");
$latest = $stmt->fetch();

if (!$latest) {
    $latest = [
        'observation_time' => 'N/A',
        'temperature' => 0,
        'humidity' => 0,
        'wind_speed' => 0,
        'cloud_cover' => 0,
        'weather_desc' => 'N/A'
    ];
}

// Icon mapper
$icon = "fa-cloud";
$weather = strtolower($latest['weather_desc']);
if (strpos($weather, 'cerah') !== false) $icon = "fa-sun text-warning";
elseif (strpos($weather, 'hujan') !== false) $icon = "fa-cloud-showers-heavy text-primary";
elseif (strpos($weather, 'berawan') !== false) $icon = "fa-cloud text-secondary";

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Monitoring Cuaca Realtime</h2>
    <span class="badge bg-success p-2 fs-6"><i class="fa-solid fa-clock me-1"></i> Update: <?= $latest['observation_time'] ?></span>
</div>

<div class="card bg-white shadow-sm border-0 mb-4 overflow-hidden">
    <div class="row g-0">
        <div class="col-md-4 bg-primary text-white p-5 d-flex flex-column align-items-center justify-content-center text-center">
            <i class="fa-solid <?= $icon ?> fa-5x mb-3 text-white"></i>
            <h3 class="fw-bold mb-0"><?= $latest['weather_desc'] ?></h3>
            <p class="mt-2 mb-0 opacity-75">Kondisi Cuaca Saat Ini</p>
        </div>
        <div class="col-md-8 p-5">
            <div class="row g-4">
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light text-danger me-3">
                            <i class="fa-solid fa-temperature-full"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Suhu Saat Ini</p>
                            <h4 class="fw-bold mb-0"><?= nf($latest['temperature'], 1) ?> &deg;C</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light text-info me-3">
                            <i class="fa-solid fa-droplet"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Kelembapan Saat Ini</p>
                            <h4 class="fw-bold mb-0"><?= nf($latest['humidity'], 1) ?> %</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light text-secondary me-3">
                            <i class="fa-solid fa-wind"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Kecepatan Angin</p>
                            <h4 class="fw-bold mb-0"><?= nf($latest['wind_speed'], 1) ?> km/h</h4>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-light text-dark me-3">
                            <i class="fa-solid fa-cloud-meatball"></i>
                        </div>
                        <div>
                            <p class="text-muted mb-0">Tutupan Awan</p>
                            <h4 class="fw-bold mb-0"><?= nf($latest['cloud_cover'], 1) ?> %</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
