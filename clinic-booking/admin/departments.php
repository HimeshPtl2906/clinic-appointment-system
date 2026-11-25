<?php
require_once '../includes/functions.php';
require_role('admin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add']) && trim($_POST['name']) !== '') {
            $pdo->prepare('INSERT INTO departments (name) VALUES (?)')->execute([trim($_POST['name'])]);
            flash('Department added.');
        } elseif (isset($_POST['delete'])) {
            $pdo->prepare('DELETE FROM departments WHERE id = ?')->execute([(int)$_POST['id']]);
            flash('Department deleted.');
        }
    } catch (PDOException $e) {
        flash('Cannot add/delete: name already exists or doctors are linked to it.', 'error');
    }
    redirect('/admin/departments.php');
}
$deps = $pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();

$pageTitle = 'Departments';
include '../includes/header.php';
?>
<div class="card">
  <h2>Departments</h2>
  <form method="post"><label>New Department</label><input name="name" required>
    <button class="btn" name="add">Add</button></form>
</div>
<div class="card"><table>
  <?php foreach ($deps as $d): ?>
  <tr><td><?= h($d['name']) ?></td><td>
    <form method="post" class="small-form" onsubmit="return confirm('Delete?')">
      <input type="hidden" name="id" value="<?= $d['id'] ?>"><button class="btn red small" name="delete">Delete</button></form></td></tr>
  <?php endforeach; ?>
</table></div>
<?php include '../includes/footer.php'; ?>
