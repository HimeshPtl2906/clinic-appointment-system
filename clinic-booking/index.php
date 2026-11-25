<?php
require_once 'includes/functions.php';
if (!empty($_SESSION['user_id'])) redirect(dashboard_for($_SESSION['role']));
$pageTitle = 'CareClinic';
include 'includes/header.php';
?>
<div class="hero card">
  <h1>Book your doctor appointment online</h1>
  <p>Choose a doctor, pick a free time slot, and get confirmation - no waiting in queue.</p>
  <a class="btn" href="<?= BASE_URL ?>/auth/register.php">Register as Patient</a>
  <a class="btn green" href="<?= BASE_URL ?>/auth/login.php">Login</a>
</div>
<?php include 'includes/footer.php'; ?>
