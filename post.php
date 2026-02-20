<?php
require_once __DIR__ . '/includes/header.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) { http_response_code(404); die("Not found"); }

$stmt = $pdo->prepare("
  SELECT p.*, u.full_name
  FROM posts p JOIN users u ON u.id=p.user_id
  WHERE p.slug=? AND p.status='published'
  LIMIT 1
");
$stmt->execute([$slug]);
$post = $stmt->fetch();
if (!$post) { http_response_code(404); die("Not found"); }

// Increase view count
$pdo->prepare("UPDATE posts SET views = views + 1 WHERE id=?")->execute([$post['id']]);

// Handle comment submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($name === '' || $email === '' || $comment === '') {
        set_flash('warning', 'Please fill all comment fields.');
        header("Location: " . BASE_URL . "/post.php?slug=" . urlencode($slug));
        exit;
    }

    $stmt = $pdo->prepare("INSERT INTO comments(post_id, name, email, comment, status) VALUES(?,?,?,?, 'pending')");
    $stmt->execute([$post['id'], $name, $email, $comment]);

    set_flash('success', 'Comment submitted! It will appear after approval.');
    header("Location: " . BASE_URL . "/post.php?slug=" . urlencode($slug));
    exit;
}

$cstmt = $pdo->prepare("SELECT * FROM comments WHERE post_id=? AND status='approved' ORDER BY created_at DESC");
$cstmt->execute([$post['id']]);
$comments = $cstmt->fetchAll();
?>

<div class="card p-4">
  <div class="text-muted small mb-2">
    <?= e(date('M d, Y', strtotime($post['created_at']))) ?> • by <?= e($post['full_name']) ?>
  </div>
  <h1 class="h3 mb-3"><?= e($post['title']) ?></h1>

  <?php if (!empty($post['featured_image'])): ?>
    <img class="img-fluid rounded-3 border mb-3" style="border-color: var(--sp-border) !important;"
         src="<?= UPLOAD_URL . e($post['featured_image']) ?>" alt="">
  <?php endif; ?>

  <div class="mb-4" style="white-space: pre-wrap;"><?= e($post['content']) ?></div>

  <hr>

  <h2 class="h5 mt-4">Comments</h2>
  <?php if (!$comments): ?>
    <p class="text-muted">No comments yet.</p>
  <?php else: ?>
    <?php foreach($comments as $c): ?>
      <div class="card p-3 my-2">
        <div class="small text-muted"><?= e($c['name']) ?> • <?= e(date('M d, Y', strtotime($c['created_at']))) ?></div>
        <div style="white-space: pre-wrap;"><?= e($c['comment']) ?></div>
      </div>
    <?php endforeach; ?>
  <?php endif; ?>

  <hr class="my-4">

  <h3 class="h5">Leave a comment</h3>
  <form method="post" class="row g-3">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="col-md-6">
      <label class="form-label">Name</label>
      <input class="form-control" name="name" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input class="form-control" name="email" type="email" required>
    </div>
    <div class="col-12">
      <label class="form-label">Comment</label>
      <textarea class="form-control" name="comment" rows="4" required></textarea>
    </div>
    <div class="col-12">
      <button class="btn btn-primary">Submit</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>