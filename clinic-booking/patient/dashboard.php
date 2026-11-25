<?php
require_once '../includes/functions.php';
require_role('patient');
$uid = $_SESSION['user_id'];

$counts = [];
foreach (['Pending','Approved','Completed','Cancelled'] as $st) {
    $q = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE patient_id = ? AND status = ?');
    $q->execute([$uid, $st]);
    $counts[$st] = $q->fetchColumn();
}
$up = $pdo->prepare("SELECT a.*, u.name AS doctor FROM appointments a
    JOIN doctors d ON d.id = a.doctor_id JOIN users u ON u.id = d.user_id
    WHERE a.patient_id = ? AND a.is_active = 1 AND a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date, a.time_slot LIMIT 5");
$up->execute([$uid]);

$pageTitle = 'Patient Dashboard';
include '../includes/header.php';
?>
<h1>Welcome, <?= h($_SESSION['name']) ?></h1>
<div class="stats">
  <?php foreach ($counts as $k => $v): ?><div class="stat"><b><?= $v ?></b><?= $k ?></div><?php endforeach; ?>
</div>
<div class="card">
  <h2>Upcoming Appointments</h2>
  <div class="table-wrap"><table>
    <tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
    <?php foreach ($up as $a): ?>
      <tr><td>Dr. <?= h($a['doctor']) ?></td><td><?= h($a['appointment_date']) ?></td>
          <td><?= substr($a['time_slot'], 0, 5) ?></td><td><span class="badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td></tr>
    <?php endforeach; if (!$up->rowCount()) echo "<tr><td colspan='4'>No upcoming appointments.</td></tr>"; ?>
  </table></div>
  <a class="btn" href="<?= BASE_URL ?>/patient/book.php">Book New Appointment</a>
</div>
<?php include '../includes/footer.php'; ?>
