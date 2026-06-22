<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$pred_result = null;
$confidence = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['predict'])) {
    $temp = (float)$_POST['temperature'];
    $hum = (float)$_POST['humidity'];
    $wind = (float)$_POST['wind_speed'];
    $cloud = (float)$_POST['cloud_cover'];
    
    // Execute python prediction script
    $output = [];
    $return_var = 0;
    
    $python_dir = __DIR__ . '/python';
    $python_cmd = file_exists('/opt/venv/bin/python') ? '/opt/venv/bin/python' : 'python';
    $script_path = $python_dir . '/predict.py';
    $cmd = escapeshellcmd("$python_cmd $script_path " . escapeshellarg($temp) . " " . escapeshellarg($hum) . " " . escapeshellarg($wind) . " " . escapeshellarg($cloud));
    exec("$cmd 2>&1", $output, $return_var);
    
    $json_string = implode("", $output);
    $result = json_decode($json_string, true);
    
    if ($result && isset($result['status']) && $result['status'] == 'success') {
        $pred_result = $result['prediction'];
        $confidence = $result['confidence'];
        
        // Save to DB with local time
        $stmt = $pdo->prepare("INSERT INTO prediction_history (temperature, humidity, wind_speed, cloud_cover, prediction_result, confidence, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$temp, $hum, $wind, $cloud, $pred_result, $confidence, date('Y-m-d H:i:s')]);
    } else {
        $error = "Prediksi gagal: " . (isset($result['message']) ? $result['message'] : htmlspecialchars($json_string));
    }
}
?>

<h2 class="mb-4">Prediksi Cuaca AI</h2>

<div class="row g-4">
    <div class="col-md-5">
        <div class="card shadow-sm border-0 p-4 h-100">
            <h5 class="fw-bold mb-4">Input Parameter</h5>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label text-muted">Suhu (&deg;C)</label>
                    <input type="number" step="0.1" name="temperature" class="form-control" required placeholder="Contoh: 28.5" value="<?= isset($_POST['temperature']) ? htmlspecialchars($_POST['temperature']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Kelembapan (%)</label>
                    <input type="number" step="0.1" name="humidity" class="form-control" required placeholder="Contoh: 75.0" value="<?= isset($_POST['humidity']) ? htmlspecialchars($_POST['humidity']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted">Kecepatan Angin (km/h)</label>
                    <input type="number" step="0.1" name="wind_speed" class="form-control" required placeholder="Contoh: 15.0" value="<?= isset($_POST['wind_speed']) ? htmlspecialchars($_POST['wind_speed']) : '' ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted">Tutupan Awan (%)</label>
                    <input type="number" step="0.1" name="cloud_cover" class="form-control" required placeholder="Contoh: 60.0" value="<?= isset($_POST['cloud_cover']) ? htmlspecialchars($_POST['cloud_cover']) : '' ?>">
                </div>
                <button type="submit" name="predict" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="fa-solid fa-wand-magic-sparkles me-2"></i> Prediksi Cuaca
                </button>
            </form>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card shadow-sm border-0 p-4 h-100 bg-primary text-white text-center d-flex flex-column justify-content-center">
            <?php if ($pred_result): ?>
                <h5 class="opacity-75 mb-4">Hasil Prediksi Sistem AI</h5>
                
                <?php
                $icon = "fa-cloud";
                $weather = strtolower($pred_result);
                if (strpos($weather, 'cerah') !== false) $icon = "fa-sun text-warning";
                elseif (strpos($weather, 'hujan') !== false) $icon = "fa-cloud-showers-heavy text-info";
                elseif (strpos($weather, 'berawan') !== false) $icon = "fa-cloud text-light";
                ?>
                
                <i class="fa-solid <?= $icon ?> fa-6x mb-4"></i>
                <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($pred_result) ?></h1>
                
                <div class="mt-4">
                    <p class="mb-1 opacity-75">Confidence Level</p>
                    <h3 class="fw-bold"><?= htmlspecialchars($confidence) ?>%</h3>
                    <div class="progress mt-2 mx-auto" style="height: 10px; width: 60%;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: <?= $confidence ?>%;" aria-valuenow="<?= $confidence ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
            <?php else: ?>
                <i class="fa-solid fa-robot fa-6x mb-4 opacity-50"></i>
                <h4 class="opacity-75">Menunggu Input Data...</h4>
                <p class="opacity-50 mt-2">Masukkan parameter cuaca di form sebelah kiri untuk melihat hasil prediksi Random Forest.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
