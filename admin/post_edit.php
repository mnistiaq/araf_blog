<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id=? LIMIT 1");
$stmt->execute([$id]);
$post = $stmt->fetch();
if (!$post) die("Post not found");

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
$pc = $pdo->prepare("SELECT category_id FROM post_categories WHERE post_id=?");
$pc->execute([$id]);
$selected = array_column($pc->fetchAll(), 'category_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $title = trim($_POST['title'] ?? '');
    $excerpt = trim($_POST['excerpt'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $status = $_POST['status'] ?? 'published';
    $chosenCats = $_POST['categories'] ?? [];

    if ($title==='' || $content==='') {
        set_flash('warning', 'Title and content are required.');
        header("Location: " . BASE_URL . "/admin/post_edit.php?id=" . $id);
        exit;
    }

    $baseSlug = slugify($title);
    $slug = unique_slug($pdo, $baseSlug, $id);

    $imgName = $post['featured_image'];
    $newImg = upload_image($_FILES['featured_image'] ?? []);
    if ($newImg) $imgName = $newImg;

    $stmt = $pdo->prepare("UPDATE posts SET title=?, slug=?, excerpt=?, content=?, featured_image=?, status=? WHERE id=?");
    $stmt->execute([$title, $slug, $excerpt, $content, $imgName, $status, $id]);

    // reset categories
    $pdo->prepare("DELETE FROM post_categories WHERE post_id=?")->execute([$id]);
    $ins = $pdo->prepare("INSERT INTO post_categories(post_id, category_id) VALUES(?,?)");
    foreach($chosenCats as $cid) $ins->execute([$id, (int)$cid]);

    set_flash('success', 'Post updated.');
    header("Location: " . BASE_URL . "/admin/index.php");
    exit;
}
?>

<div class="card p-4">
  <h1 class="h5 mb-3">Edit Post</h1>
  <form method="post" enctype="multipart/form-data" class="row g-3">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <div class="col-12">
      <label class="form-label">Title</label>
      <input class="form-control" name="title" value="<?= e($post['title']) ?>" required>
    </div>

    <div class="col-12">
      <label class="form-label">Excerpt</label>
      <input class="form-control" name="excerpt" value="<?= e($post['excerpt'] ?? '') ?>" maxlength="300">
    </div>

    <div class="col-12">
      <label class="form-label">Content</label>
      <textarea class="form-control" name="content" rows="10" required><?= e($post['content']) ?></textarea>
    </div>

    <div class="col-md-6">
      <label class="form-label">Replace Featured Image</label>
      <input class="form-control" type="file" name="featured_image" accept="image/*">
      <?php if (!empty($post['featured_image'])): ?>
        <div class="text-muted small mt-2">Current: <?= e($post['featured_image']) ?></div>
      <?php endif; ?>
    </div>

    <div class="col-md-6">
      <label class="form-label">Status</label>
      <select class="form-select" name="status">
        <option value="published" <?= $post['status']==='published'?'selected':'' ?>>Published</option>
        <option value="draft" <?= $post['status']==='draft'?'selected':'' ?>>Draft</option>
      </select>
    </div>

    <div class="col-12">
      <label class="form-label">Categories</label>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach($cats as $c): ?>
          <?php $checked = in_array($c['id'], $selected) ? 'checked' : ''; ?>
          <label class="sp-badge">
            <input type="checkbox" name="categories[]" value="<?= (int)$c['id'] ?>" <?= $checked ?>>
            <?= e($c['name']) ?>
          </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div class="col-12 d-flex gap-2">
      <button class="btn btn-primary">Update</button>
      <a class="btn sp-btn" href="<?= BASE_URL ?>/admin/index.php">Cancel</a>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>