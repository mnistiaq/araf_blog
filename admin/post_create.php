<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'published';
    $chosenCats = $_POST['categories'] ?? [];

    if ($title==='' || $content==='') {
        set_flash('warning', 'Title and content are required.');
        header("Location: " . BASE_URL . "/admin/post_create.php");
        exit;
    }

    $baseSlug = slugify($title);
    $slug = unique_slug($pdo, $baseSlug);

    $imgName = upload_image($_FILES['featured_image'] ?? []);

    $stmt = $pdo->prepare("INSERT INTO posts(user_id, title, slug, excerpt, content, featured_image, status) VALUES(?,?,?,?,?,?,?)");
    $stmt->execute([current_user_id(), $title, $slug, $excerpt, $content, $imgName, $status]);
    $postId = (int)$pdo->lastInsertId();

    // categories
    $pc = $pdo->prepare("INSERT INTO post_categories(post_id, category_id) VALUES(?,?)");
    foreach ($chosenCats as $cid) {
        $pc->execute([$postId, (int)$cid]);
    }

    set_flash('success', 'Post created.');
    header("Location: " . BASE_URL . "/admin/index.php");
    exit;
}
?>

<div class="card p-4">
  <h1 class="h5 mb-3">Create Post</h1>
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="col-12">
      <label class="form-label">Title</label>
      <input class="form-control" name="title" required>
    </div>

    <div class="col-12">
      <label class="form-label">Excerpt (short summary)</label>
      <input class="form-control" name="excerpt" maxlength="300">
    </div>

    <div class="col-12">
      <label class="form-label">Content</label>
      <textarea class="form-control" name="content" rows="10" required></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">Featured Image (jpg/png/webp)</label>
      <input class="form-control" type="file" name="featured_image" accept="image/*">
    </div>

    <div class="col-md-6">
      <label class="form-label">Status</label>
      <select class="form-select" name="status">
        <option value="published">Published</option>
        <option value="draft">Draft</option>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label">Categories</label>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach($cats as $c): ?>
          <label class="sp-badge">
            <input type="checkbox" name="categories[]" value="<?= (int)$c['id'] ?>"> <?= e($c['name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary">Save</button>
      <a class="btn sp-btn" href="<?= BASE_URL ?>/admin/index.php">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>