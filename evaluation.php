<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Fetch the latest evaluation
$stmt = $pdo->query("SELECT * FROM model_evaluation ORDER BY id DESC LIMIT 1");
$eval = $stmt->fetch();

?>

<h2 class="mb-4">Evaluasi Model AI</h2>

<?php if ($eval): ?>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="card stat-card success h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-success text-white me-3">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Accuracy</h6>
                    <h3 class="mb-0 fw-bold"><?= nf($eval['accuracy'] * 100, 2) ?>%</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card primary h-100 p-3" style="border-left-color: #0d6efd;">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-primary text-white me-3">
                    <i class="fa-solid fa-crosshairs"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Precision</h6>
                    <h3 class="mb-0 fw-bold"><?= nf($eval['precision_score'] * 100, 2) ?>%</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card warning h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-warning text-white me-3">
                    <i class="fa-solid fa-rotate-left"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Recall</h6>
                    <h3 class="mb-0 fw-bold"><?= nf($eval['recall_score'] * 100, 2) ?>%</h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card info h-100 p-3">
            <div class="d-flex align-items-center">
                <div class="icon-box bg-info text-white me-3">
                    <i class="fa-solid fa-balance-scale"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">F1-Score</h6>
                    <h3 class="mb-0 fw-bold"><?= nf($eval['f1_score'] * 100, 2) ?>%</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 p-4">
            <h4 class="fw-bold mb-4 text-center">Confusion Matrix</h4>
            <div class="text-center">
                <?php if (file_exists('assets/images/confusion_matrix.png')): ?>
                    <img src="assets/images/confusion_matrix.png" alt="Confusion Matrix" class="img-fluid rounded shadow-sm border">
                <?php else: ?>
                    <div class="alert alert-warning">Confusion matrix image not found. Please train the model first.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
    <div class="alert alert-info">Belum ada data evaluasi. Silakan <a href="training.php" class="fw-bold">latih model</a> terlebih dahulu.</div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
