<?php
require_once '../includes/functions.php';
require_role('doctor');
$docId = current_doctor_id($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = (int)$_POST['id'];
    $action = $_POST['action'] ?? '';

    if ($action === 'approve') {
        $pdo->prepare("UPDATE appointments SET status='Approved' WHERE id=? AND doctor_id=? AND status='Pending'")->execute([$id, $docId]);
        flash('Appointment approved.');
    } elseif ($action === 'reject') {
        $pdo->prepare("UPDATE appointments SET status='Rejected', is_active=NULL WHERE id=? AND doctor_id=? AND status='Pending'")->execute([$id, $docId]);
        flash('Appointment rejected.');
    } elseif ($action === 'complete') {
        $q = $pdo->prepare("UPDATE appointments SET status='Completed' WHERE id=? AND doctor_id=? AND status='Approved'");
        $q->execute([$id, $docId]);
        $notes = trim($_POST['notes'] ?? '');
        if ($q->rowCount() && $notes !== '') {
            $pdo->prepare('INSERT INTO prescriptions (appointment_id, notes) VALUES (?, ?)')->execute([$id, $notes]);
        }
        flash('Appointment marked completed.');
    }
    redirect('/doctor/dashboard.php');
}

$s = $pdo->prepare("SELECT a.*, u.name AS patient, u.phone FROM appointments a
    JOIN users u ON u.id = a.patient_id WHERE a.doctor_id = ?
    ORDER BY FIELD(a.status,'Pending','Approved','Completed','Rejected','Cancelled'), a.appointment_date, a.time_slot");
$s->execute([$docId]);
$rows = $s->fetchAll();

$pageTitle = 'Doctor Dashboard';
include '../includes/header.php';
?>
<h1>Dr. <?= h($_SESSION['name']) ?> - Appointments</h1>
<div class="card"><div class="table-wrap"><table>
  <tr><th>Patient</th><th>Date</th><th>Time</th><th>Reason</th><th>Status</th><th>Action</th></tr>
  <?php foreach ($rows as $a): ?>
  <tr>
    <td><?= h($a['patient']) ?><br><small><?= h($a['phone']) ?></small></td>
    <td><?= h($a['appointment_date']) ?></td>
    <td><?= substr($a['time_slot'], 0, 5) ?></td>
    <td><?= h($a['reason']) ?></td>
    <td><span class="badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td>
    <td>
      <?php if ($a['status'] === 'Pending'): ?>
        <form method="post" class="small-form"><input type="hidden" name="id" value="<?= $a['id'] ?>">
          <button name="action" value="approve" class="btn green small">Approve</button>
          <button name="action" value="reject" class="btn red small">Reject</button></form>
      <?php elseif ($a['status'] === 'Approved'): ?>
        <form method="post"><input type="hidden" name="id" value="<?= $a['id'] ?>">
          <textarea name="notes" rows="2" placeholder="Prescription / notes"></textarea>
          <button name="action" value="complete" class="btn small">Mark Completed</button></form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; if (!$rows) echo "<tr><td colspan='6'>No appointments yet.</td></tr>"; ?>
</table></div></div>
<?php include '../includes/footer.php'; ?>
