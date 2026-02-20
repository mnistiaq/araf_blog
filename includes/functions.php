<?php
// includes/functions.php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

function require_login(): void {
    if (!is_logged_in()) {
        header("Location: " . BASE_URL . "/login.php");
        exit;
    }
}

function current_user_id(): ?int {
    return is_logged_in() ? (int)$_SESSION['user']['id'] : null;
}

function set_flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $f;
    }
    return null;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_verify(): void {
    $token = $_POST['csrf'] ?? '';
    if (!$token || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $token)) {
        die("Invalid CSRF token.");
    }
}

function slugify(string $text): string {
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/i', '-', $text);
    $text = trim($text, '-');
    return $text ?: 'post';
}

function unique_slug(PDO $pdo, string $baseSlug, int $ignorePostId = 0): string {
    $slug = $baseSlug;
    $i = 1;
    while (true) {
        $sql = "SELECT id FROM posts WHERE slug = ? " . ($ignorePostId ? "AND id != ?" : "");
        $stmt = $pdo->prepare($sql);
        $ignorePostId ? $stmt->execute([$slug, $ignorePostId]) : $stmt->execute([$slug]);
        if (!$stmt->fetch()) break;
        $slug = $baseSlug . '-' . $i;
        $i++;
    }
    return $slug;
}

function paginate(int $total, int $page, int $perPage): array {
    $totalPages = max(1, (int)ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    return [$page, $totalPages, $offset];
}

function upload_image(array $file): ?string {
    if (empty($file['name'])) return null;
    if ($file['error'] !== UPLOAD_ERR_OK) return null;

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $mime = mime_content_type($file['tmp_name']);
    if (!isset($allowed[$mime])) return null;

    if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);

    $ext = $allowed[$mime];
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest = UPLOAD_DIR . $name;

    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return $name;
    }
    return null;
}