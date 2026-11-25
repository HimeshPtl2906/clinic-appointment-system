<?php
require_once '../includes/functions.php';
require_role('patient');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctorId = (int)($_POST['doctor_id'] ?? 0);
    $date     = $_POST['date'] ?? '';
    $slot     = $_POST['slot'] ?? '';
    $reason   = trim($_POST['reason'] ?? '');

    if (!$doctorId || $date < date('Y-m-d') || !in_array($slot, get_available_slots($pdo, $doctorId, $date))) {
        flash('Invalid or unavailable slot. Please choose again.', 'error');
    } else {
        try {
            $pdo->prepare('INSERT INTO appointments (patient_id, doctor_id, appointment_date, time_slot, reason) VALUES (?,?,?,?,?)')
                ->execute([$_SESSION['user_id'], $doctorId, $date, $slot . ':00', $reason]);
            flash('Appointment booked! Waiting for doctor approval.');
            redirect('/patient/my_appointments.php');
        } catch (PDOException $e) {
            // UNIQUE key violation = kisi aur ne same slot abhi book kar liya
            flash($e->getCode() == 23000 ? 'Sorry, this slot was just booked by someone else.' : 'Booking failed.', 'error');
        }
    }
}

$docs = $pdo->query("SELECT d.id, d.fees, u.name, dep.name AS dept FROM doctors d
    JOIN users u ON u.id = d.user_id JOIN departments dep ON dep.id = d.department_id
    ORDER BY dep.name, u.name")->fetchAll();

$pageTitle = 'Book Appointment';
include '../includes/header.php';
?>
<div class="card">
  <h2>Book Appointment</h2>
  <form method="post">
    <label>Doctor</label>
    <select name="doctor_id" id="doctor" required>
      <option value="">-- Select doctor --</option>
      <?php $cur = ''; foreach ($docs as $d):
        if ($cur !== $d['dept']) { if ($cur !== '') echo '</optgroup>'; echo '<optgroup label="' . h($d['dept']) . '">'; $cur = $d['dept']; } ?>
        <option value="<?= $d['id'] ?>">Dr. <?= h($d['name']) ?> (Fees: Rs. <?= h($d['fees']) ?>)</option>
      <?php endforeach; if ($cur !== '') echo '</optgroup>'; ?>
    </select>

    <label>Date</label>
    <input type="date" name="date" id="date" min="<?= date('Y-m-d') ?>" required>

    <label>Available Slots</label>
    <div class="slots" id="slots"><em>Select doctor and date first.</em></div>

    <label>Reason (optional)</label>
    <textarea name="reason" rows="2" maxlength="250"></textarea>

    <button class="btn">Confirm Booking</button>
  </form>
</div>

<script>
const doctor = document.getElementById('doctor');
const date   = document.getElementById('date');
const box    = document.getElementById('slots');

async function loadSlots() {
  if (!doctor.value || !date.value) return;
  box.innerHTML = 'Loading...';
  const res = await fetch('get_slots.php?doctor_id=' + doctor.value + '&date=' + date.value);
  const slots = await res.json();
  if (!slots.length) { box.innerHTML = '<em>No slots available for this day.</em>'; return; }
  box.innerHTML = slots.map(s =>
    '<label><input type="radio" name="slot" value="' + s + '" required><span>' + s + '</span></label>').join('');
}
doctor.addEventListener('change', loadSlots);
date.addEventListener('change', loadSlots);
</script>
<?php include '../includes/footer.php'; ?>
