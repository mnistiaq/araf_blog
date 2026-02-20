<?php
require_once __DIR__ . '/../includes/header.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verify();
    $name = trim($_POST['name'] ?? '');
    if ($name !== '') {
        $slug = slugify($name);
        try {
            $pdo->prepare("INSERT INTO categories(name, slug) VALUES(?,?)")->execute([$name, $slug]);
            set_flash('success', 'Category added.');
        } catch (Exception $e) {
            set_flash('danger', 'Category already exists.');
        }
    }
    header("Location: " . BASE_URL . "/admin/categories.php");
    exit;
}

if (isset($_GET['del'])) {
    $cid = (int)$_GET['del'];
    $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$cid]);
    set_flash('success', 'Category deleted.');
    header("Location: " . BASE_URL . "/admin/categories.php");
    exit;
}

$cats = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
?>

<div class="row g-3">
  <div class="col-lg-5">
    <div class="card p-4">
      <h1 class="h5 mb-3">Add Category</h1>
      <form method="post" class="d-flex gap-2">
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <input class="form-control" name="name" placeholder="e.g. Tech, Notes..." required>
        <button class="btn btn-primary">Add</button>
      </form>
      <div class="mt-3">
        <a class="btn sp-btn btn-sm" href="<?= BASE_URL ?>/admin/index.php">Back</a>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card p-4">
      <h2 class="h5 mb-3">Categories</h2>
      <div class="table-responsive">
        <table class="table table-dark table-borderless mb-0">
          <thead><tr class="text-muted"><th>Name</th><th>Slug</th><th class="text-end">Action</th></tr></thead>
          <tbody>
            <?php foreach($cats as $c): ?>
              <tr>
                <td><?= e($c['name']) ?></td>
                <td class="text-muted"><?= e($c['slug']) ?></td>
                <td class="text-end">
                  <a class="btn btn-sm sp-btn" href="?del=<?= (int)$c['id'] ?>" onclick="return confirm('Delete category?')">Delete</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>