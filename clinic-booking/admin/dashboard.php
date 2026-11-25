<?php
require_once '../includes/functions.php';
require_role('admin');

$stat = [
  'Patients'     => $pdo->query("SELECT COUNT(*) FROM users WHERE role='patient'")->fetchColumn(),
  'Doctors'      => $pdo->query('SELECT COUNT(*) FROM doctors')->fetchColumn(),
  'Appointments' => $pdo->query('SELECT COUNT(*) FROM appointments')->fetchColumn(),
  'Pending'      => $pdo->query("SELECT COUNT(*) FROM appointments WHERE status='Pending'")->fetchColumn(),
];
$rows = $pdo->query("SELECT a.*, p.name AS patient, du.name AS doctor FROM appointments a
    JOIN users p ON p.id = a.patient_id JOIN doctors d ON d.id = a.doctor_id
    JOIN users du ON du.id = d.user_id ORDER BY a.created_at DESC LIMIT 50")->fetchAll();

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
?>
<h1>Admin Dashboard</h1>
<div class="stats"><?php foreach ($stat as $k => $v): ?><div class="stat"><b><?= $v ?></b><?= $k ?></div><?php endforeach; ?></div>
<div class="card"><h2>Recent Appointments</h2><div class="table-wrap"><table>
  <tr><th>Patient</th><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th></tr>
  <?php foreach ($rows as $a): ?>
  <tr><td><?= h($a['patient']) ?></td><td>Dr. <?= h($a['doctor']) ?></td><td><?= h($a['appointment_date']) ?></td>
      <td><?= substr($a['time_slot'], 0, 5) ?></td><td><span class="badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td></tr>
  <?php endforeach; if (!$rows) echo "<tr><td colspan='5'>No appointments yet.</td></tr>"; ?>
</table></div></div>
<?php include '../includes/footer.php'; ?>
