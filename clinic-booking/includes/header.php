<?php require_once __DIR__ . '/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($pageTitle ?? 'Clinic Booking') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/style.css">
</head>
<body>
<nav class="nav">
  <a class="brand" href="<?= BASE_URL ?>/index.php">+ CareClinic</a>
  <div class="links">
  <?php if (!empty($_SESSION['user_id'])): $r = $_SESSION['role']; ?>
    <a href="<?= BASE_URL . dashboard_for($r) ?>">Dashboard</a>
    <?php if ($r === 'patient'): ?>
      <a href="<?= BASE_URL ?>/patient/book.php">Book</a>
      <a href="<?= BASE_URL ?>/patient/my_appointments.php">My Appointments</a>
    <?php elseif ($r === 'doctor'): ?>
      <a href="<?= BASE_URL ?>/doctor/schedule.php">My Schedule</a>
    <?php else: ?>
      <a href="<?= BASE_URL ?>/admin/doctors.php">Doctors</a>
      <a href="<?= BASE_URL ?>/admin/departments.php">Departments</a>
    <?php endif; ?>
    <a href="<?= BASE_URL ?>/auth/logout.php">Logout (<?= h($_SESSION['name']) ?>)</a>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/auth/login.php">Login</a>
    <a href="<?= BASE_URL ?>/auth/register.php">Register</a>
  <?php endif; ?>
  </div>
</nav>
<main class="container">
<?= flash() ?>
