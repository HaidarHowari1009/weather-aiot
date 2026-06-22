<?php
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'includes/sidebar.php';

// Pagination settings
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search & Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$date_filter = isset($_GET['date_filter']) ? trim($_GET['date_filter']) : '';

$where_clauses = [];
$params = [];

if ($search !== '') {
    $where_clauses[] = "(weather_desc LIKE :search)";
    $params[':search'] = "%$search%";
}

if ($date_filter !== '') {
    $where_clauses[] = "DATE(observation_time) = :date";
    $params[':date'] = $date_filter;
}

$where_sql = count($where_clauses) > 0 ? "WHERE " . implode(" AND ", $where_clauses) : "";

// Count total
$count_sql = "SELECT COUNT(*) FROM weather_data $where_sql";
$stmt = $pdo->prepare($count_sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$total_results = $stmt->fetchColumn();
$total_pages = ceil($total_results / $limit);

// Fetch data
$sql = "SELECT * FROM weather_data $where_sql ORDER BY observation_time DESC LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$dataset = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0">Dataset Historis</h2>
    <a href="api/export_csv.php?search=<?= urlencode($search) ?>&date_filter=<?= urlencode($date_filter) ?>" class="btn btn-success"><i class="fa-solid fa-file-csv me-2"></i>Export CSV</a>
</div>

<div class="card p-4 shadow-sm border-0">
    <form method="GET" class="row g-3 mb-4">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Cari deskripsi cuaca..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-4">
            <input type="date" name="date_filter" class="form-control" value="<?= htmlspecialchars($date_filter) ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-magnifying-glass me-2"></i>Filter</button>
        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Waktu Pengamatan</th>
                    <th>Suhu (&deg;C)</th>
                    <th>Kelembapan (%)</th>
                    <th>Kec. Angin (km/h)</th>
                    <th>Tutupan Awan (%)</th>
                    <th>Kondisi Cuaca</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($dataset) > 0): ?>
                    <?php foreach ($dataset as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['observation_time']) ?></td>
                            <td><?= nf($row['temperature'], 1) ?></td>
                            <td><?= nf($row['humidity'], 1) ?></td>
                            <td><?= nf($row['wind_speed'], 1) ?></td>
                            <td><?= nf($row['cloud_cover'], 1) ?></td>
                            <td><span class="badge bg-secondary"><?= htmlspecialchars($row['weather_desc']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted">Data tidak ditemukan.</td>
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
                    <a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&date_filter=<?= urlencode($date_filter) ?>">Previous</a>
                </li>
                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&date_filter=<?= urlencode($date_filter) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&date_filter=<?= urlencode($date_filter) ?>">Next</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
