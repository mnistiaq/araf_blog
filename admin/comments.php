<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $pdo->prepare("UPDATE comments SET status='approved' WHERE id=?")->execute([$id]);
    set_flash('success', 'Comment approved.');
    header("Location: " . BASE_URL . "/admin/comments.php");
    exit;
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM comments WHERE id=?")->execute([$id]);
    set_flash('success', 'Comment deleted.');
    header("Location: " . BASE_URL . "/admin/comments.php");
    exit;
}

$comments = $pdo->query("
  SELECT c.*, p.title AS post_title
  FROM comments c
  JOIN posts p ON p.id=c.post_id
  ORDER BY c.created_at DESC
  LIMIT 50
")->fetchAll();
?>

<div class="card p-4">
  <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
    <h1 class="h5 mb-0">Comments</h1>
    <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/index.php">Back</a>
  </div>

  <div class="table-responsive">
    <table class="table table-dark table-borderless align-middle mb-0">
      <thead>
        <tr class="text-muted">
          <th>Post</th><th>Name</th><th>Status</th><th>Date</th><th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($comments as $c): ?>
          <tr>
            <td><?= e($c['post_title']) ?></td>
            <td>
              <?= e($c['name']) ?>
              <div class="text-muted small"><?= e($c['email']) ?></div>
              <div class="small" style="white-space: pre-wrap;"><?= e($c['comment']) ?></div>
            </td>
            <td><span class="sp-badge"><?= e($c['status']) ?></span></td>
            <td class="text-muted"><?= e(date('M d, Y', strtotime($c['created_at']))) ?></td>
            <td class="text-end">
              <?php if ($c['status'] === 'pending'): ?>
                <a class="btn btn-sm btn-primary" href="?approve=<?= (int)$c['id'] ?>">Approve</a>
              <?php endif; ?>
              <a class="btn btn-sm sp-btn" href="?delete=<?= (int)$c['id'] ?>" onclick="return confirm('Delete comment?')">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>