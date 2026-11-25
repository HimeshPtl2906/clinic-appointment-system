<?php
require_once '../includes/functions.php';
require_role('doctor');
$docId = current_doctor_id($pdo);
$days  = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('DELETE FROM schedules WHERE doctor_id = ?')->execute([$docId]);
    $ins = $pdo->prepare('INSERT INTO schedules (doctor_id, day_of_week, start_time, end_time) VALUES (?,?,?,?)');
    foreach ($days as $n => $name) {
        if (!empty($_POST['on'][$n])) {
            $st = $_POST['start'][$n]; $en = $_POST['end'][$n];
            if ($st && $en && $st < $en) $ins->execute([$docId, $n, $st, $en]);
        }
    }
    flash('Schedule saved.');
    redirect('/doctor/schedule.php');
}

$s = $pdo->prepare('SELECT * FROM schedules WHERE doctor_id = ?');
$s->execute([$docId]);
$sch = [];
foreach ($s as $r) $sch[$r['day_of_week']] = $r;

$pageTitle = 'My Schedule';
include '../includes/header.php';
?>
<div class="card">
  <h2>Weekly Availability</h2>
  <form method="post">
    <div class="table-wrap"><table>
      <tr><th>Available</th><th>Day</th><th>From</th><th>To</th></tr>
      <?php foreach ($days as $n => $name): $r = $sch[$n] ?? null; ?>
      <tr>
        <td><input type="checkbox" name="on[<?= $n ?>]" <?= $r ? 'checked' : '' ?> style="width:auto"></td>
        <td><?= $name ?></td>
        <td><input type="time" name="start[<?= $n ?>]" value="<?= $r ? substr($r['start_time'],0,5) : '10:00' ?>"></td>
        <td><input type="time" name="end[<?= $n ?>]" value="<?= $r ? substr($r['end_time'],0,5) : '17:00' ?>"></td>
      </tr>
      <?php endforeach; ?>
    </table></div>
    <button class="btn">Save Schedule</button>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
