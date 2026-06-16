<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

$message = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['train_model'])) {
    // Run Python script
    // Note: Assuming 'python' is in the system PATH. Use absolute path if necessary (e.g., 'C:\\Python310\\python.exe')
    $output = [];
    $return_var = 0;
    
    // Change directory to python folder so it saves .pkl and images correctly
    chdir('python');
    exec("python train_model.py 2>&1", $output, $return_var);
    chdir('..');
    
    $json_string = implode("", $output);
    $result = json_decode($json_string, true);
    
    if ($result && isset($result['status'])) {
        if ($result['status'] == 'success') {
            $status = 'success';
            $message = "Model berhasil dilatih! Accuracy: " . $result['metrics']['accuracy'] . "%";
        } else {
            $status = 'danger';
            $message = "Error: " . $result['message'];
        }
    } else {
        $status = 'danger';
        $message = "Gagal mengeksekusi script Python. Output: " . htmlspecialchars($json_string);
    }
}

// Get the latest evaluation date if exists
$stmt = $pdo->query("SELECT created_at FROM model_evaluation ORDER BY id DESC LIMIT 1");
$latest_eval = $stmt->fetchColumn();

?>

<h2 class="mb-4">Training Model Machine Learning</h2>

<?php if ($message): ?>
    <div class="alert alert-<?= $status ?> alert-dismissible fade show" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-body p-5 text-center">
        <div class="mb-4">
            <i class="fa-solid fa-brain text-primary" style="font-size: 6rem;"></i>
        </div>
        <h4 class="fw-bold">Random Forest Classifier</h4>
        <p class="text-muted mb-4">
            Sistem menggunakan algoritma Random Forest untuk memprediksi kondisi cuaca berdasarkan data historis (Suhu, Kelembapan, Kecepatan Angin, Tutupan Awan).
            Tekan tombol di bawah untuk melatih ulang model dengan data terbaru.
        </p>
        
        <?php if ($latest_eval): ?>
            <p class="text-success small fw-bold"><i class="fa-solid fa-check-circle me-1"></i> Model terakhir dilatih pada: <?= $latest_eval ?></p>
        <?php endif; ?>

        <form method="POST">
            <button type="submit" name="train_model" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm mt-3">
                <i class="fa-solid fa-cogs me-2"></i> Mulai Training Model
            </button>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
