<?php
// AJAX endpoint: JSON me khali slots return karta hai
require_once '../includes/functions.php';
require_role('patient');
header('Content-Type: application/json');

$doctorId = (int)($_GET['doctor_id'] ?? 0);
$date     = $_GET['date'] ?? '';
if (!$doctorId || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date < date('Y-m-d')) {
    echo json_encode([]); exit;
}
echo json_encode(get_available_slots($pdo, $doctorId, $date));
