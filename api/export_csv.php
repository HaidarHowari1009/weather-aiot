<?php
require_once '../config/database.php';

// Pagination settings
$limit = 99999; // Unlimited for export
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

// Fetch all data for export
$sql = "SELECT * FROM weather_data $where_sql ORDER BY observation_time DESC";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->execute();
$dataset = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="weather_data_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 to display special characters correctly in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row
fputcsv($output, [
    'Waktu Pengamatan',
    'Suhu (°C)',
    'Kelembapan (%)',
    'Kec. Angin (km/h)',
    'Tutupan Awan (%)',
    'Kondisi Cuaca'
], ';');

// Write data rows
foreach ($dataset as $row) {
    fputcsv($output, [
        $row['observation_time'],
        number_format($row['temperature'], 1, '.', ''),
        number_format($row['humidity'], 1, '.', ''),
        number_format($row['wind_speed'], 1, '.', ''),
        number_format($row['cloud_cover'], 1, '.', ''),
        $row['weather_desc']
    ], ';');
}

fclose($output);
exit;
?>
