<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch statistics
$totalData = $pdo->query("SELECT COUNT(*) FROM weather_data")->fetchColumn();
$avgTemp = $pdo->query("SELECT AVG(temperature) FROM weather_data")->fetchColumn();
$avgHumidity = $pdo->query("SELECT AVG(humidity) FROM weather_data")->fetchColumn();

// Count days (weather conditions)
$sunnyDays = $pdo->query("SELECT COUNT(*) FROM weather_data WHERE weather_desc LIKE '%Cerah%'")->fetchColumn();
$rainyDays = $pdo->query("SELECT COUNT(*) FROM weather_data WHERE weather_desc LIKE '%Hujan%'")->fetchColumn();
$cloudyDays = $pdo->query("SELECT COUNT(*) FROM weather_data WHERE weather_desc LIKE 'Berawan%' OR weather_desc = 'Berawan'")->fetchColumn();

?>

<h2 class="mb-4">Dashboard Analytics</h2>

<div class="row g-4 mb-4">
    <div class="col-md-4 col-xl-3">
        <div class="card stat-card info h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-info text-white me-3">
                    <i class="fa-solid fa-database"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Data Historis</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($totalData) ?></h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-xl-3">
        <div class="card stat-card danger h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-danger text-white me-3">
                    <i class="fa-solid fa-temperature-half"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Rata-rata Suhu</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($avgTemp, 1) ?> &deg;C</h3>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 col-xl-3">
        <div class="card stat-card warning h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-warning text-white me-3">
                    <i class="fa-solid fa-droplet"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Rata-rata Kelembapan</h6>
                    <h3 class="mb-0 fw-bold"><?= number_format($avgHumidity, 1) ?> %</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<h4 class="mb-3 mt-5">Distribusi Cuaca Historis</h4>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 p-3 bg-light border-0">
            <div class="text-center">
                <i class="fa-solid fa-sun text-warning fa-3x mb-3 mt-2"></i>
                <h5 class="fw-bold">Hari Cerah</h5>
                <h2 class="text-warning fw-bold mb-0"><?= number_format($sunnyDays) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-3 bg-light border-0">
            <div class="text-center">
                <i class="fa-solid fa-cloud text-secondary fa-3x mb-3 mt-2"></i>
                <h5 class="fw-bold">Hari Berawan</h5>
                <h2 class="text-secondary fw-bold mb-0"><?= number_format($cloudyDays) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100 p-3 bg-light border-0">
            <div class="text-center">
                <i class="fa-solid fa-cloud-showers-heavy text-primary fa-3x mb-3 mt-2"></i>
                <h5 class="fw-bold">Hari Hujan</h5>
                <h2 class="text-primary fw-bold mb-0"><?= number_format($rainyDays) ?></h2>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
