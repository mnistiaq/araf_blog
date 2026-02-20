<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

// Stats
$totalPosts = (int)$pdo->query("SELECT COUNT(*) c FROM posts")->fetch()['c'];
$totalComments = (int)$pdo->query("SELECT COUNT(*) c FROM comments")->fetch()['c'];
$pendingComments = (int)$pdo->query("SELECT COUNT(*) c FROM comments WHERE status='pending'")->fetch()['c'];

$posts = $pdo->query("
  SELECT p.id, p.title, p.status, p.created_at
  FROM posts p
  ORDER BY p.created_at DESC
  LIMIT 8
")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
  <div>
    <h1 class="h4 mb-1">Dashboard</h1>
    <div class="text-muted small">Welcome, <?= e($_SESSION['user']['full_name']) ?></div>
  </div>
  <a class="btn btn-primary" href="<?= BASE_URL ?>/admin/post_create.php">+ New Post</a>
</div>

<div class="row g-3 mb-4">
  <div class="col-md-4"><div class="card p-3"><div class="text-muted small">Posts</div><div class="h4 mb-0"><?= $totalPosts ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><div class="text-muted small">Comments</div><div class="h4 mb-0"><?= $totalComments ?></div></div></div>
  <div class="col-md-4"><div class="card p-3"><div class="text-muted small">Pending</div><div class="h4 mb-0"><?= $pendingComments ?></div></div></div>
</div>

<div class="card p-4">
  <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <h2 class="h5 mb-0">Recent Posts</h2>
    <div class="d-flex gap-2">
      <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/categories.php">Categories</a>
      <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/comments.php">Comments</a>
      <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/profile.php">Profile</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="text-muted">
          <th>Title</th><th>Status</th><th>Date</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($posts as $p): ?>
          <tr>
            <td><?= e($p['title']) ?></td>
            <td><span class="sp-badge"><?= e($p['status']) ?></span></td>
            <td class="text-muted"><?= e(date('M d, Y', strtotime($p['created_at']))) ?></td>
            <td class="text-end">
              <a class="btn btn-sm sp-btn" href="<?= BASE_URL ?>/admin/post_edit.php?id=<?= (int)$p['id'] ?>">Edit</a>
              <a class="btn btn-sm sp-btn" href="<?= BASE_URL ?>/admin/post_delete.php?id=<?= (int)$p['id'] ?>"
                 onclick="return confirm('Delete this post?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>