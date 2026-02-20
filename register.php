<?php
require_once __DIR__ . '/includes/header.php';

if (is_logged_in()) {
  header("Location: " . BASE_URL . "/admin/index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $full = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($full==='' || $username==='' || $email==='' || $pass==='') {
        set_flash('warning', 'All fields are required.');
        header("Location: " . BASE_URL . "/register.php");
        exit;
    }

    $hash = password_hash($pass, PASSWORD_DEFAULT);

    try {
        $stmt = $pdo->prepare("INSERT INTO users(full_name, username, email, password_hash, role) VALUES(?,?,?,?, 'admin')");
        $stmt->execute([$full, $username, $email, $hash]);
        set_flash('success', 'Account created. Please login.');
        header("Location: " . BASE_URL . "/login.php");
        exit;
    } catch (Exception $e) {
        set_flash('danger', 'Username or email already exists.');
        header("Location: " . BASE_URL . "/register.php");
        exit;
    }
}
?>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card p-4">
      <h1 class="h4 mb-3">Register (Admin)</h1>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="col-12">
          <label class="form-label">Full Name</label>
          <input class="form-control" name="full_name" required>
        </div>
        <div class="col-6">
          <label class="form-label">Username</label>
          <input class="form-control" name="username" required>
        </div>
        <div class="col-6">
          <label class="form-label">Email</label>
          <input class="form-control" name="email" type="email" required>
        </div>
        <div class="col-12">
          <label class="form-label">Password</label>
          <input class="form-control" name="password" type="password" required>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Create Account</button>
          <a class="btn sp-btn" href="<?= BASE_URL ?>/login.php">Login</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>