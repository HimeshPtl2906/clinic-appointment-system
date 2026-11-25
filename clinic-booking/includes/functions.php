<?php
require_once __DIR__ . '/../config/db.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }   // XSS se bachav

function redirect($path) { header('Location: ' . BASE_URL . $path); exit; }

function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return; }
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash']; unset($_SESSION['flash']);
        return "<div class='alert $t'>" . h($m) . "</div>";
    }
    return '';
}

function require_role($role) {
    if (empty($_SESSION['user_id']) || $_SESSION['role'] !== $role) redirect('/auth/login.php');
}

function current_doctor_id(PDO $pdo) {
    $s = $pdo->prepare('SELECT id FROM doctors WHERE user_id = ?');
    $s->execute([$_SESSION['user_id']]);
    return $s->fetchColumn();
}

function dashboard_for($role) { return "/$role/dashboard.php"; }

// Doctor ke us din ke khali slots nikalta hai (schedule - booked - past time)
function get_available_slots(PDO $pdo, $doctorId, $date) {
    $dow = (int)date('N', strtotime($date));
    $s = $pdo->prepare('SELECT start_time, end_time FROM schedules WHERE doctor_id = ? AND day_of_week = ?');
    $s->execute([$doctorId, $dow]);
    $sch = $s->fetch();
    if (!$sch) return [];

    $b = $pdo->prepare('SELECT time_slot FROM appointments WHERE doctor_id = ? AND appointment_date = ? AND is_active = 1');
    $b->execute([$doctorId, $date]);
    $booked = array_map(fn($t) => substr($t, 0, 5), $b->fetchAll(PDO::FETCH_COLUMN));

    $slots = [];
    $t   = strtotime("$date {$sch['start_time']}");
    $end = strtotime("$date {$sch['end_time']}");
    while ($t + SLOT_MINUTES * 60 <= $end) {
        $label = date('H:i', $t);
        if (!in_array($label, $booked) && $t > time()) $slots[] = $label;
        $t += SLOT_MINUTES * 60;
    }
    return $slots;
}
