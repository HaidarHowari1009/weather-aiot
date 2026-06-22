<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Pagination for history
$limit = 15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

function formatLocalTime($timestamp) {
    try {
        $dt = new DateTime($timestamp, new DateTimeZone('UTC'));
        $dt->setTimezone(new DateTimeZone(date_default_timezone_get()));
        return $dt->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        return $timestamp;
    }
}

$total_results = $pdo->query("SELECT COUNT(*) FROM prediction_history")->fetchColumn();
$total_pages = ceil($total_results / $limit);

$stmt = $pdo->prepare("SELECT * FROM prediction_history ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$history = $stmt->fetchAll();

?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Riwayat Prediksi</h2>
    <a href="api/export_history_csv.php" class="btn btn-success"><i class="fa-solid fa-file-csv me-2"></i>Export CSV</a>
</div>

<div class="card p-4 shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Waktu Prediksi</th>
                    <th>Input Suhu</th>
                    <th>Input Kelembapan</th>
                    <th>Input Angin</th>
                    <th>Input Awan</th>
                    <th>Hasil Prediksi</th>
                    <th>Confidence</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($history) > 0): ?>
                    <?php foreach ($history as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars(formatLocalTime($row['created_at'])) ?></td>
                            <td><?= number_format($row['temperature'], 1) ?> &deg;C</td>
                            <td><?= number_format($row['humidity'], 1) ?> %</td>
                            <td><?= number_format($row['wind_speed'], 1) ?> km/h</td>
                            <td><?= number_format($row['cloud_cover'], 1) ?> %</td>
                            <td><span class="badge bg-primary"><?= htmlspecialchars($row['prediction_result']) ?></span></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="me-2 fw-bold"><?= number_format($row['confidence'], 1) ?>%</span>
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-success" style="width: <?= $row['confidence'] ?>%;"></div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Belum ada riwayat prediksi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
