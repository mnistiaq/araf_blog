<?php
require_once __DIR__ . '/includes/header.php';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$perPage = 6;

// Count published posts
$stmt = $pdo->query("SELECT COUNT(*) AS c FROM posts WHERE status='published'");
$total = (int)$stmt->fetch()['c'];
[$page, $totalPages, $offset] = paginate($total, $page, $perPage);

// Fetch posts
$stmt = $pdo->prepare("
  SELECT p.*, u.full_name
  FROM posts p
  JOIN users u ON u.id = p.user_id
  WHERE p.status='published'
  ORDER BY p.created_at DESC
  LIMIT ? OFFSET ?
");
$stmt->bindValue(1, $perPage, PDO::PARAM_INT);
$stmt->bindValue(2, $offset, PDO::PARAM_INT);
$stmt->execute();
$posts = $stmt->fetchAll();
?>

<div class="row g-4">
  <div class="col-lg-8">
    <div class="card p-4 mb-4">
      <h1 class="h3 mb-2">Welcome</h1>
      <p class="text-muted mb-0">
        I’m <strong>m n istiaq</strong> — CSE (Batch 10, Session 2022–2023), University of Barishal.
        I write about manga, history, and cycling — and sometimes about student life.
      </p>
    </div>

    <?php foreach ($posts as $post): ?>
      <div class="card p-4 mb-3">
        <div class="d-flex justify-content-between flex-wrap gap-2">
          <div class="text-muted small">
            <?= e(date('M d, Y', strtotime($post['created_at']))) ?> • by <?= e($post['full_name']) ?> • <?= (int)$post['views'] ?> views
          </div>
          <div>
            <?php
              $cstmt = $pdo->prepare("
                SELECT c.name, c.slug
                FROM categories c
                JOIN post_categories pc ON pc.category_id=c.id
                WHERE pc.post_id=?
                LIMIT 3
              ");
              $cstmt->execute([$post['id']]);
              foreach ($cstmt->fetchAll() as $c):
            ?>
              <a class="sp-badge" href="<?= BASE_URL ?>/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
          </div>
        </div>

        <h2 class="h4 mt-2 mb-2">
          <a href="<?= BASE_URL ?>/post.php?slug=<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
        </h2>

        <?php if (!empty($post['featured_image'])): ?>
          <img class="img-fluid rounded-3 border mt-2" style="border-color: var(--sp-border) !important;"
               src="<?= UPLOAD_URL . e($post['featured_image']) ?>" alt="">
        <?php endif; ?>

        <p class="text-muted mt-3 mb-0"><?= e($post['excerpt'] ?? '') ?></p>
        <div class="mt-3">
          <a class="btn btn-primary btn-sm" href="<?= BASE_URL ?>/post.php?slug=<?= e($post['slug']) ?>">Read</a>
        </div>
      </div>
    <?php endforeach; ?>

    <nav class="mt-4">
      <ul class="pagination pagination-sm">
        <?php for ($i=1; $i<=$totalPages; $i++): ?>
          <li class="page-item <?= $i===$page ? 'active' : '' ?>">
            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
      </ul>
    </nav>
  </div>

  <div class="col-lg-4">
    <div class="card p-4 mb-3">
      <h3 class="h5 mb-3">Search</h3>
      <form method="get" action="<?= BASE_URL ?>/search.php" class="d-flex gap-2">
        <input class="form-control" name="q" placeholder="Search posts...">
        <button class="btn btn-primary">Go</button>
      </form>
    </div>

    <div class="card p-4">
      <h3 class="h5 mb-3">Categories</h3>
      <?php
        $cats = $pdo->query("SELECT name, slug FROM categories ORDER BY name ASC")->fetchAll();
      ?>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach($cats as $c): ?>
          <a class="sp-badge" href="<?= BASE_URL ?>/category.php?slug=<?= e($c['slug']) ?>"><?= e($c['name']) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>