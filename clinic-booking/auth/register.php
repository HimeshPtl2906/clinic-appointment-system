<?php
require_once '../includes/functions.php';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name === '') $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (!preg_match('/^[0-9]{10}$/', $phone)) $errors[] = 'Phone must be 10 digits.';
    if (strlen($pass) < 6) $errors[] = 'Password must be at least 6 characters.';

    if (!$errors) {
        $c = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $c->execute([$email]);
        if ($c->fetch()) {
            $errors[] = 'This email is already registered.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);   // password kabhi plain save nahi hota
            $pdo->prepare("INSERT INTO users (name,email,password,phone,role) VALUES (?,?,?,?, 'patient')")
                ->execute([$name, $email, $hash, $phone]);
            flash('Registration successful. Please login.');
            redirect('/auth/login.php');
        }
    }
}
$pageTitle = 'Register';
include '../includes/header.php';
?>
<div class="card narrow">
  <h2>Patient Registration</h2>
  <?php foreach ($errors as $e) echo "<div class='alert error'>" . h($e) . "</div>"; ?>
  <form method="post">
    <label>Full Name</label><input name="name" value="<?= h($_POST['name'] ?? '') ?>" required>
    <label>Email</label><input type="email" name="email" value="<?= h($_POST['email'] ?? '') ?>" required>
    <label>Phone</label><input name="phone" maxlength="10" value="<?= h($_POST['phone'] ?? '') ?>" required>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn">Register</button>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
