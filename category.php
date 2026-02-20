<?php
require_once __DIR__ . '/includes/header.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) die("Category missing");

$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug=? LIMIT 1");
$stmt->execute([$slug]);
$cat = $stmt->fetch();
if (!$cat) die("Category not found");

$stmt = $pdo->prepare("
  SELECT p.*
  FROM posts p
  JOIN post_categories pc ON pc.post_id=p.id
  WHERE pc.category_id=? AND p.status='published'
  ORDER BY p.created_at DESC
");
$stmt->execute([$cat['id']]);
$posts = $stmt->fetchAll();
?>

<div class="card p-4 mb-3">
  <h1 class="h4 mb-0">Category: <?= e($cat['name']) ?></h1>
</div>

<?php foreach($posts as $p): ?>
  <div class="card p-4 mb-3">
    <h2 class="h5 mb-1"><a href="<?= BASE_URL ?>/post.php?slug=<?= e($p['slug']) ?>"><?= e($p['title']) ?></a></h2>
    <p class="text-muted mb-0"><?= e($p['excerpt'] ?? '') ?></p>
  </div>
<?php endforeach; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>