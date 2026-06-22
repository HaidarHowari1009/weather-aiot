<?php
require_once '../config/database.php';

// Fetch all prediction history records
$sql = "SELECT * FROM prediction_history ORDER BY created_at DESC";
$stmt = $pdo->query($sql);
$history = $stmt->fetchAll();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="prediction_history_' . date('Y-m-d_H-i-s') . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Add BOM for UTF-8 to display special characters correctly in Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Write header row
fputcsv($output, [
    'Waktu Prediksi',
    'Suhu (°C)',
    'Kelembapan (%)',
    'Kecepatan Angin (km/h)',
    'Tutupan Awan (%)',
    'Hasil Prediksi',
    'Confidence (%)'
], ';');

// Write data rows
foreach ($history as $row) {
    fputcsv($output, [
        $row['created_at'],
        nf($row['temperature'], 1, '.', ''),
        nf($row['humidity'], 1, '.', ''),
        nf($row['wind_speed'], 1, '.', ''),
        nf($row['cloud_cover'], 1, '.', ''),
        $row['prediction_result'],
        nf($row['confidence'], 1, '.', '')
    ], ';');
}

fclose($output);
exit;
