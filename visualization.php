<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch recent 14 records for line charts
$stmt = $pdo->query("SELECT strftime('%Y-%m-%d', observation_time) as date, AVG(temperature) as avg_temp, AVG(humidity) as avg_humidity, AVG(wind_speed) as avg_wind FROM weather_data GROUP BY strftime('%Y-%m-%d', observation_time) ORDER BY date DESC LIMIT 14");
$trendData = array_reverse($stmt->fetchAll());

$labels = [];
$temps = [];
$humidities = [];
$winds = [];

foreach ($trendData as $row) {
    $labels[] = $row['date'];
    $temps[] = round($row['avg_temp'], 1);
    $humidities[] = round($row['avg_humidity'], 1);
    $winds[] = round($row['avg_wind'], 1);
}

// Fetch weather distribution
$distStmt = $pdo->query("SELECT weather_desc, COUNT(*) as count FROM weather_data GROUP BY weather_desc");
$distData = $distStmt->fetchAll();

$distLabels = [];
$distCounts = [];
$distColors = [];

$colorMap = [
    'Cerah' => '#ffc107',
    'Cerah Berawan' => '#ffca2c',
    'Berawan' => '#6c757d',
    'Berawan Tebal' => '#495057',
    'Hujan Ringan' => '#0dcaf0',
    'Hujan Sedang' => '#0d6efd',
    'Hujan Lebat' => '#084298',
    'Hujan Petir' => '#6610f2'
];

foreach ($distData as $row) {
    $desc = $row['weather_desc'];
    $distLabels[] = $desc;
    $distCounts[] = $row['count'];
    $distColors[] = isset($colorMap[$desc]) ? $colorMap[$desc] : '#adb5bd';
}

?>

<h2 class="mb-4">Visualisasi Data Cuaca</h2>

<div class="row g-4 mb-4">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm border-0 h-100">
            <h5 class="fw-bold mb-3">Tren Suhu (14 Hari Terakhir)</h5>
            <canvas id="tempChart" height="100"></canvas>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-4 shadow-sm border-0 h-100">
            <h5 class="fw-bold mb-3">Distribusi Kondisi Cuaca</h5>
            <canvas id="distChart" height="200"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3">Tren Kelembapan (%)</h5>
            <canvas id="humidityChart" height="120"></canvas>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card p-4 shadow-sm border-0">
            <h5 class="fw-bold mb-3">Kecepatan Angin (km/h)</h5>
            <canvas id="windChart" height="120"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const labels = <?= json_encode($labels) ?>;
    
    // Temperature Chart
    new Chart(document.getElementById('tempChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Suhu Rata-rata (°C)',
                data: <?= json_encode($temps) ?>,
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: { responsive: true }
    });

    // Distribution Chart
    new Chart(document.getElementById('distChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($distLabels) ?>,
            datasets: [{
                data: <?= json_encode($distCounts) ?>,
                backgroundColor: <?= json_encode($distColors) ?>,
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    // Humidity Chart
    new Chart(document.getElementById('humidityChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kelembapan (%)',
                data: <?= json_encode($humidities) ?>,
                backgroundColor: '#0dcaf0',
                borderRadius: 4
            }]
        },
        options: { responsive: true }
    });

    // Wind Chart
    new Chart(document.getElementById('windChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Kecepatan Angin (km/h)',
                data: <?= json_encode($winds) ?>,
                borderColor: '#6c757d',
                borderWidth: 2,
                borderDash: [5, 5],
                tension: 0.1
            }]
        },
        options: { responsive: true }
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
