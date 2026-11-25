<?php
require_once '../includes/functions.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete'])) {
        // users row delete karne se doctor + appointments cascade me delete ho jate hain
        $pdo->prepare("DELETE u FROM users u JOIN doctors d ON d.user_id = u.id WHERE d.id = ?")->execute([(int)$_POST['id']]);
        flash('Doctor removed.');
    } else {
        $name = trim($_POST['name']); $email = trim($_POST['email']); $pass = $_POST['password'];
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
            flash('Fill all fields correctly (password min 6 chars).', 'error');
        } else {
            try {
                $pdo->beginTransaction();   // dono insert saath me ho ya dono nahi
                $pdo->prepare("INSERT INTO users (name,email,password,phone,role) VALUES (?,?,?,?, 'doctor')")
                    ->execute([$name, $email, password_hash($pass, PASSWORD_DEFAULT), trim($_POST['phone'])]);
                $pdo->prepare('INSERT INTO doctors (user_id, department_id, experience, fees) VALUES (?,?,?,?)')
                    ->execute([$pdo->lastInsertId(), (int)$_POST['department_id'], (int)$_POST['experience'], (float)$_POST['fees']]);
                $pdo->commit();
                flash('Doctor added. Doctor can login and set schedule.');
            } catch (PDOException $e) {
                $pdo->rollBack();
                flash('Could not add doctor (email may already exist).', 'error');
            }
        }
    }
    redirect('/admin/doctors.php');
}

$deps = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
$docs = $pdo->query("SELECT d.*, u.name, u.email, dep.name AS dept FROM doctors d
    JOIN users u ON u.id = d.user_id JOIN departments dep ON dep.id = d.department_id ORDER BY u.name")->fetchAll();

$pageTitle = 'Manage Doctors';
include '../includes/header.php';
?>
<div class="card">
  <h2>Add Doctor</h2>
  <form method="post">
    <label>Name</label><input name="name" required>
    <label>Email (login)</label><input type="email" name="email" required>
    <label>Password</label><input type="password" name="password" required>
    <label>Phone</label><input name="phone" maxlength="10">
    <label>Department</label>
    <select name="department_id" required><?php foreach ($deps as $d) echo "<option value='{$d['id']}'>" . h($d['name']) . "</option>"; ?></select>
    <label>Experience (years)</label><input type="number" name="experience" min="0" value="1">
    <label>Fees (Rs.)</label><input type="number" name="fees" min="0" value="300">
    <button class="btn">Add Doctor</button>
  </form>
</div>
<div class="card"><h2>All Doctors</h2><div class="table-wrap"><table>
  <tr><th>Name</th><th>Email</th><th>Department</th><th>Exp</th><th>Fees</th><th></th></tr>
  <?php foreach ($docs as $d): ?>
  <tr><td>Dr. <?= h($d['name']) ?></td><td><?= h($d['email']) ?></td><td><?= h($d['dept']) ?></td>
      <td><?= $d['experience'] ?> yr</td><td>Rs. <?= h($d['fees']) ?></td>
      <td><form method="post" class="small-form" onsubmit="return confirm('Remove doctor and all their appointments?')">
        <input type="hidden" name="id" value="<?= $d['id'] ?>"><button class="btn red small" name="delete">Remove</button></form></td></tr>
  <?php endforeach; ?>
</table></div></div>
<?php include '../includes/footer.php'; ?>
