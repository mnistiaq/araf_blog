<?php
require_once __DIR__ . '/../includes/functions.php';
require_login();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$pdo->prepare("DELETE FROM posts WHERE id=?")->execute([$id]);

set_flash('success', 'Post deleted.');
header("Location: " . BASE_URL . "/admin/index.php");
exit;