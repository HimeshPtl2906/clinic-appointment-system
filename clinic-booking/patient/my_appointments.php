<?php
require_once '../includes/functions.php';
require_role('patient');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cancel: slot free karne ke liye is_active = NULL
    $q = $pdo->prepare("UPDATE appointments SET status='Cancelled', is_active=NULL
        WHERE id = ? AND patient_id = ? AND status IN ('Pending','Approved')");
    $q->execute([(int)$_POST['id'], $_SESSION['user_id']]);
    flash($q->rowCount() ? 'Appointment cancelled.' : 'Cannot cancel this appointment.', $q->rowCount() ? 'success' : 'error');
    redirect('/patient/my_appointments.php');
}

$s = $pdo->prepare("SELECT a.*, u.name AS doctor, dep.name AS dept, p.notes AS prescription
    FROM appointments a
    JOIN doctors d ON d.id = a.doctor_id JOIN users u ON u.id = d.user_id
    JOIN departments dep ON dep.id = d.department_id
    LEFT JOIN prescriptions p ON p.appointment_id = a.id
    WHERE a.patient_id = ? ORDER BY a.appointment_date DESC, a.time_slot DESC");
$s->execute([$_SESSION['user_id']]);
$rows = $s->fetchAll();

$pageTitle = 'My Appointments';
include '../includes/header.php';
?>
<div class="card">
  <h2>My Appointments</h2>
  <div class="table-wrap"><table>
    <tr><th>Doctor</th><th>Date</th><th>Time</th><th>Status</th><th>Prescription</th><th></th></tr>
    <?php foreach ($rows as $a): ?>
    <tr>
      <td>Dr. <?= h($a['doctor']) ?><br><small><?= h($a['dept']) ?></small></td>
      <td><?= h($a['appointment_date']) ?></td>
      <td><?= substr($a['time_slot'], 0, 5) ?></td>
      <td><span class="badge <?= $a['status'] ?>"><?= $a['status'] ?></span></td>
      <td><?= nl2br(h($a['prescription'] ?? '-')) ?></td>
      <td><?php if (in_array($a['status'], ['Pending','Approved'])): ?>
        <form method="post" class="small-form" onsubmit="return confirm('Cancel this appointment?')">
          <input type="hidden" name="id" value="<?= $a['id'] ?>">
          <button class="btn red small">Cancel</button>
        </form><?php endif; ?></td>
    </tr>
    <?php endforeach; if (!$rows) echo "<tr><td colspan='6'>No appointments yet.</td></tr>"; ?>
  </table></div>
</div>
<?php include '../includes/footer.php'; ?>
