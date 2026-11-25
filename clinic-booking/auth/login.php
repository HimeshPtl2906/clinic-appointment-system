<?php
require_once '../includes/functions.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $s = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $s->execute([trim($_POST['email'] ?? '')]);
    $u = $s->fetch();

    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);   // session fixation se bachav
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['name']    = $u['name'];
        $_SESSION['role']    = $u['role'];
        redirect(dashboard_for($u['role']));
    }
    $error = 'Invalid email or password.';
}
$pageTitle = 'Login';
include '../includes/header.php';
?>
<div class="card narrow">
  <h2>Login</h2>
  <?php if ($error) echo "<div class='alert error'>" . h($error) . "</div>"; ?>
  <form method="post">
    <label>Email</label><input type="email" name="email" required>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn">Login</button>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
