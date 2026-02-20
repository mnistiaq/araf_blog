<?php
require_once __DIR__ . '/includes/header.php';

if (is_logged_in()) {
  header("Location: " . BASE_URL . "/admin/index.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $user = trim($_POST['user'] ?? '');
    $pass = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username=? OR email=? LIMIT 1");
    $stmt->execute([$user, $user]);
    $u = $stmt->fetch();

    if ($u && password_verify($pass, $u['password_hash'])) {
        $_SESSION['user'] = [
            'id' => $u['id'],
            'full_name' => $u['full_name'],
            'role' => $u['role']
        ];
        set_flash('success', 'Logged in.');
        header("Location: " . BASE_URL . "/admin/index.php");
        exit;
    }

    set_flash('danger', 'Invalid credentials.');
    header("Location: " . BASE_URL . "/login.php");
    exit;
}
?>

<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card p-4">
      <h1 class="h4 mb-3">Login</h1>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="col-12">
          <label class="form-label">Username or Email</label>
          <input class="form-control" name="user" required>
        </div>
        <div class="col-12">
          <label class="form-label">Password</label>
          <input class="form-control" name="password" type="password" required>
        </div>
        <div class="col-12 d-flex gap-2">
          <button class="btn btn-primary">Login</button>
          <a class="btn sp-btn" href="<?= BASE_URL ?>/register.php">Register</a>
        </div>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>