<?php
require_once __DIR__ . '/includes/header.php';

$q = trim($_GET['q'] ?? '');
$posts = [];

if ($q !== '') {
    $stmt = $pdo->prepare("
      SELECT * FROM posts
      WHERE status='published' AND (title LIKE ? OR content LIKE ?)
      ORDER BY created_at DESC
      LIMIT 30
    ");
    $like = "%{$q}%";
    $stmt->execute([$like, $like]);
    $posts = $stmt->fetchAll();
}
?>

<div class="card p-4 mb-3">
  <h1 class="h4 mb-1">Search</h1>
  <p class="text-muted mb-0">Results for: <strong><?= e($q) ?></strong></p>
</div>

<?php if ($q === ''): ?>
  <p class="text-muted">Type something to search.</p>
<?php elseif (!$posts): ?>
  <p class="text-muted">No posts found.</p>
<?php else: ?>
  <?php foreach($posts as $p): ?>
    <div class="card p-4 mb-3">
      <h2 class="h5 mb-1"><a href="<?= BASE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h2>
      <p class="text-muted mb-0"><?= e($p['excerpt'] ?? '') ?></p>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>