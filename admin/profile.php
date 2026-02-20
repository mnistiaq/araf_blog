<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

$uid = current_user_id();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->execute([$uid]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $bio = trim($_POST['bio'] ?? '');
    $newPass = $_POST['new_password'] ?? '';

    $pdo->prepare("UPDATE users SET bio=? WHERE id=?")->execute([$bio, $uid]);

    if ($newPass !== '') {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password_hash=? WHERE id=?")->execute([$hash, $uid]);
    }

    set_flash('success', 'Profile updated.');
    header("Location: " . BASE_URL . "/admin/profile.php");
    exit;
}
?>

<div class="card p-4">
  <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <h1 class="h5 mb-0">Profile</h1>
    <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/index.php">Back</a>
  </div>

  <form method="post" class="row g-3">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="col-12">
      <label class="form-label">Bio</label>
      <textarea class="form-control" name="bio" rows="5"><?= e($user['bio'] ?? '') ?></textarea>
      <div class="text-muted small mt-1">This can show on your About page later if you want.</div>
    </div>

    <div class="col-md-6">
      <label class="form-label">New Password (optional)</label>
      <input class="form-control" name="new_password" type="password">
    </div>

    <div class="col-12">
      <button class="btn btn-primary">Save</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>