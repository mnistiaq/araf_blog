<?php
require_once __DIR__ . '/functions.php';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e(APP_NAME) ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark sp-nav border-bottom">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="<?= BASE_URL ?>/index.php"><?= e(APP_NAME) ?></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/index.php">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/about.php">About</a></li>
        <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/contact.php">Contact</a></li>
        <?php if (is_logged_in()): ?>
          <li class="nav-item"><a class="nav-link" href="<?= BASE_URL ?>/admin/index.php">Dashboard</a></li>
          <li class="nav-item"><a class="btn btn-sm sp-btn" href="<?= BASE_URL ?>/logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-sm sp-btn" href="<?= BASE_URL ?>/login.php">Login</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<main class="container my-4">
  <?php if ($flash): ?>
    <div class="alert alert-<?= e($flash['type']) ?> sp-alert" role="alert">
      <?= e($flash['msg']) ?>
    </div>
  <?php endif; ?>