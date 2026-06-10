<?php
/**
 * =====================================================
 *  মসজিদ মাদ্রাসার খেদমত সেন্টার — API
 *  File   : api.php
 *  Place  : /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
 *  Author : Generated for khedmotcenter.com
 * =====================================================
 *
 *  ── Actions handled ──────────────────────────────
 *  Auth      : check_session, user_login, register,
 *              admin_login, logout
 *  News      : get_public_news, get_user_dashboard
 *  Admin     : admin_create_post, admin_get_posts,
 *              admin_edit_post, admin_delete_post
 *  Users     : admin_get_users, admin_create_user,
 *              admin_edit_user, admin_delete_user,
 *              admin_extend_expiry
 *  Job       : submit_job_post
 * =====================================================
 */

/* ──────────────────────────────────────────────────
   1. HEADERS
   ────────────────────────────────────────────────── */
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

/* ──────────────────────────────────────────────────
   2. DATABASE CONFIG  (পরিবর্তন করুন)
   ────────────────────────────────────────────────── */
define('DB_HOST', 'localhost');
define('DB_NAME', 'khedmotdb');
define('DB_USER', 'khedmotdb');       // আপনার MySQL user
define('DB_PASS', 'YOUR_DB_PASSWORD'); // আপনার MySQL password
define('DB_CHARSET', 'utf8mb4');

/* ──────────────────────────────────────────────────
   3. ADMIN CONFIG  (পরিবর্তন করুন)
   ────────────────────────────────────────────────── */
define('ADMIN_EMAIL',    'admin@khedmotcenter.com'); // এডমিন ইমেইল
define('ADMIN_PASSWORD', 'YOUR_ADMIN_PASSWORD');     // এডমিন পাসওয়ার্ড (plain বা hash)

/* ──────────────────────────────────────────────────
   4. IMAGE UPLOAD CONFIG
   ────────────────────────────────────────────────── */
define('UPLOAD_DIR',  __DIR__ . '/uploads/posts/');
define('UPLOAD_URL',  '/uploads/posts/');
define('MAX_IMG_SIZE', 2 * 1024 * 1024); // 2 MB

/* ──────────────────────────────────────────────────
   5. SESSION START
   ────────────────────────────────────────────────── */
session_set_cookie_params([
    'lifetime' => 86400 * 7,   // 7 দিন
    'path'     => '/',
    'secure'   => false,        // HTTPS থাকলে true করুন
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

/* ──────────────────────────────────────────────────
   6. HELPERS
   ────────────────────────────────────────────────── */

/** JSON response পাঠিয়ে script শেষ করে */
function resp(bool $success, array $extra = [], int $code = 200): never {
    http_response_code($code);
    echo json_encode(array_merge(['success' => $success], $extra), JSON_UNESCAPED_UNICODE);
    exit;
}

/** DB connection (singleton) */
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    return $pdo;
}

/** Session-এ লগইন সেট করে */
function setSession(string $role, int $id, string $name): void {
    session_regenerate_id(true);
    $_SESSION['role'] = $role;
    $_SESSION['id']   = $id;
    $_SESSION['name'] = $name;
}

/** Mobile number মাস্ক করে (শেষ ৩ সংখ্যা ***) */
function maskMobile(string $text): string {
    return preg_replace('/(01[3-9]\d{5})(\d{3})/', '$1***', $text);
}

/** Base64 image decode করে disk-এ সেভ করে, URL ফেরত দেয় */
function saveBase64Image(?string $base64): ?string {
    if (!$base64) return null;

    // data:image/...;base64, অংশ বাদ দিয়ে নিন
    if (preg_match('/^data:image\/(\w+);base64,/', $base64, $m)) {
        $ext  = strtolower($m[1]) === 'jpeg' ? 'jpg' : strtolower($m[1]);
        $data = base64_decode(substr($base64, strpos($base64, ',') + 1));
    } else {
        return null;
    }

    // শুধু jpg, png, gif, webp অনুমোদিত
    if (!in_array($ext, ['jpg', 'png', 'gif', 'webp'])) return null;

    if (strlen($data) > MAX_IMG_SIZE) return null;

    if (!is_dir(UPLOAD_DIR)) {
        mkdir(UPLOAD_DIR, 0755, true);
    }

    $filename = uniqid('post_', true) . '.' . $ext;
    $path     = UPLOAD_DIR . $filename;
    file_put_contents($path, $data);

    return UPLOAD_URL . $filename;
}

/* ──────────────────────────────────────────────────
   7. REQUEST PARSE
   ────────────────────────────────────────────────── */
$raw    = file_get_contents('php://input');
$body   = json_decode($raw, true) ?? [];
$action = trim($body['action'] ?? '');

if (!$action) {
    resp(false, ['message' => 'action প্রয়োজন।'], 400);
}

/* ──────────────────────────────────────────────────
   8. ROUTE — action অনুযায়ী handler call
   ────────────────────────────────────────────────── */
try {
    match($action) {
        'check_session'       => actionCheckSession(),
        'user_login'          => actionUserLogin($body),
        'register'            => actionRegister($body),
        'admin_login'         => actionAdminLogin($body),
        'logout'              => actionLogout(),

        'get_public_news'     => actionGetPublicNews(),
        'get_user_dashboard'  => actionGetUserDashboard(),

        'admin_create_post'   => actionAdminCreatePost($body),
        'admin_get_posts'     => actionAdminGetPosts(),
        'admin_edit_post'     => actionAdminEditPost($body),
        'admin_delete_post'   => actionAdminDeletePost($body),

        'admin_get_users'     => actionAdminGetUsers($body),
        'admin_create_user'   => actionAdminCreateUser($body),
        'admin_edit_user'     => actionAdminEditUser($body),
        'admin_delete_user'   => actionAdminDeleteUser($body),
        'admin_extend_expiry' => actionAdminExtendExpiry($body),

        'submit_job_post'     => actionSubmitJobPost($body),

        default               => resp(false, ['message' => 'অজানা action: ' . $action], 400),
    };
} catch (PDOException $e) {
    // Database error — user-friendly বার্তা, detail log-এ
    error_log('[KhedmotAPI] DB error on action=' . $action . ': ' . $e->getMessage());
    resp(false, ['message' => 'ডেটাবেস সমস্যা হয়েছে। একটু পরে চেষ্টা করুন।'], 500);
} catch (Throwable $e) {
    error_log('[KhedmotAPI] Error on action=' . $action . ': ' . $e->getMessage());
    resp(false, ['message' => 'সার্ভার সমস্যা হয়েছে।'], 500);
}

/* ══════════════════════════════════════════════════
   ──  HANDLERS  ──────────────────────────────────
   ══════════════════════════════════════════════════ */

/* ── AUTH ─────────────────────────────────────────── */

/**
 * check_session
 * Return: { success, role, id, name }
 */
function actionCheckSession(): never {
    if (!empty($_SESSION['role'])) {
        resp(true, [
            'role' => $_SESSION['role'],
            'id'   => $_SESSION['id'],
            'name' => $_SESSION['name'],
        ]);
    }
    resp(false, ['message' => 'লগইন নেই।']);
}

/**
 * user_login
 * Input: { mobile, password }
 * Return: { success, role, name }
 */
function actionUserLogin(array $b): never {
    $mobile   = trim($b['mobile']   ?? '');
    $password = trim($b['password'] ?? '');

    if (!$mobile || !$password) {
        resp(false, ['message' => 'মোবাইল ও পাসওয়ার্ড দিন।']);
    }

    $st = db()->prepare('SELECT id, name, password FROM users WHERE mobile = ? LIMIT 1');
    $st->execute([$mobile]);
    $user = $st->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        resp(false, ['message' => 'মোবাইল নাম্বার বা পাসওয়ার্ড সঠিক নয়।']);
    }

    setSession('user', $user['id'], $user['name']);
    resp(true, ['role' => 'user', 'name' => $user['name']]);
}

/**
 * register
 * Input: { name, address, mobile, password }
 * Return: { success }
 */
function actionRegister(array $b): never {
    $name     = trim($b['name']     ?? '');
    $address  = trim($b['address']  ?? '');
    $mobile   = trim($b['mobile']   ?? '');
    $password = trim($b['password'] ?? '');

    if (!$name || !$address || !$mobile || !$password) {
        resp(false, ['message' => 'সকল তথ্য পূরণ করুন।']);
    }
    if (!preg_match('/^01[3-9]\d{8}$/', $mobile)) {
        resp(false, ['message' => 'সঠিক মোবাইল নাম্বার দিন।']);
    }
    if (strlen($password) < 6) {
        resp(false, ['message' => 'পাসওয়ার্ড কমপক্ষে ৬ অক্ষরের হতে হবে।']);
    }

    // Duplicate mobile check
    $ck = db()->prepare('SELECT id FROM users WHERE mobile = ? LIMIT 1');
    $ck->execute([$mobile]);
    if ($ck->fetch()) {
        resp(false, ['message' => 'এই মোবাইল নাম্বারে আগেই অ্যাকাউন্ট আছে।']);
    }

    // Default expiry: ২০ দিন পরে
    $expiry = date('Y-m-d', strtotime('+20 days'));

    $ins = db()->prepare(
        'INSERT INTO users (name, mobile, address, password, expiry_date, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );
    $ins->execute([$name, $mobile, $address, password_hash($password, PASSWORD_DEFAULT), $expiry]);

    resp(true, ['message' => 'অ্যাকাউন্ট তৈরি হয়েছে!']);
}

/**
 * admin_login
 * Input: { email, password }
 * Return: { success, role }
 */
function actionAdminLogin(array $b): never {
    $email    = trim($b['email']    ?? '');
    $password = trim($b['password'] ?? '');

    if (!$email || !$password) {
        resp(false, ['message' => 'ইমেইল ও পাসওয়ার্ড প্রয়োজন।']);
    }

    if ($email !== ADMIN_EMAIL) {
        resp(false, ['message' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।']);
    }

    // ADMIN_PASSWORD plain-text বা hashed দুটোই support করে
    $ok = ADMIN_PASSWORD === $password
       || password_verify($password, ADMIN_PASSWORD);

    if (!$ok) {
        resp(false, ['message' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।']);
    }

    setSession('admin', 0, 'Admin');
    resp(true, ['role' => 'admin']);
}

/**
 * logout
 */
function actionLogout(): never {
    $_SESSION = [];
    session_destroy();
    resp(true);
}

/* ── NEWS / DASHBOARD ─────────────────────────────── */

/**
 * get_public_news
 * লগইন ছাড়াই দেখা যায়, তবে মোবাইল নম্বর mask করা থাকে
 * Return: { success, posts: [{id, content, image_path, created_at}] }
 */
function actionGetPublicNews(): never {
    $st = db()->query(
        'SELECT id, content, image_path, created_at
           FROM posts
          ORDER BY created_at DESC
          LIMIT 50'
    );
    $posts = $st->fetchAll();

    // মোবাইল নম্বর মাস্ক করা (শেষ ৩ সংখ্যা ***)
    foreach ($posts as &$p) {
        $p['content'] = maskMobile($p['content']);
    }
    unset($p);

    resp(true, ['posts' => $posts]);
}

/**
 * get_user_dashboard
 * লগইন user-এর তথ্য ও সম্পূর্ণ (unmask) পোস্ট
 * Return: { success, user, days_left, is_active, posts }
 */
function actionGetUserDashboard(): never {
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'user') {
        resp(false, ['message' => 'লগইন প্রয়োজন।'], 401);
    }

    $userId = (int) $_SESSION['id'];

    $st = db()->prepare(
        'SELECT id, name, mobile, address, expiry_date, created_at
           FROM users
          WHERE id = ? LIMIT 1'
    );
    $st->execute([$userId]);
    $user = $st->fetch();

    if (!$user) {
        resp(false, ['message' => 'ইউজার পাওয়া যায়নি।']);
    }

    $today    = new DateTime('today');
    $expiry   = new DateTime($user['expiry_date']);
    $diff     = (int) $today->diff($expiry)->days * ($expiry >= $today ? 1 : -1);
    $isActive = $expiry >= $today;

    // সম্পূর্ণ posts (মোবাইল নম্বর unmask)
    $ps = db()->query(
        'SELECT id, content, image_path, created_at
           FROM posts
          ORDER BY created_at DESC
          LIMIT 50'
    );

    resp(true, [
        'user'      => $user,
        'days_left' => $diff,
        'is_active' => $isActive,
        'posts'     => $ps->fetchAll(),
    ]);
}

/* ── ADMIN POSTS ──────────────────────────────────── */

/**
 * admin_create_post
 * Input: { content, image_base64? }
 */
function actionAdminCreatePost(array $b): never {
    requireAdmin();
    $content = trim($b['content'] ?? '');
    if (!$content) {
        resp(false, ['message' => 'পোস্টের বিষয়বস্তু লিখুন।']);
    }

    $imagePath = saveBase64Image($b['image_base64'] ?? null);

    $ins = db()->prepare(
        'INSERT INTO posts (content, image_path, created_at) VALUES (?, ?, NOW())'
    );
    $ins->execute([$content, $imagePath]);
    $id = db()->lastInsertId();

    resp(true, ['id' => $id, 'image_path' => $imagePath]);
}

/**
 * admin_get_posts
 * Return: { success, posts }
 */
function actionAdminGetPosts(): never {
    requireAdmin();
    $st = db()->query(
        'SELECT id, content, image_path, created_at
           FROM posts
          ORDER BY created_at DESC'
    );
    resp(true, ['posts' => $st->fetchAll()]);
}

/**
 * admin_edit_post
 * Input: { id, content }
 */
function actionAdminEditPost(array $b): never {
    requireAdmin();
    $id      = (int) ($b['id']      ?? 0);
    $content = trim($b['content']  ?? '');
    if (!$id || !$content) {
        resp(false, ['message' => 'id এবং content প্রয়োজন।']);
    }
    $up = db()->prepare('UPDATE posts SET content = ? WHERE id = ?');
    $up->execute([$content, $id]);
    resp(true);
}

/**
 * admin_delete_post
 * Input: { id }
 */
function actionAdminDeletePost(array $b): never {
    requireAdmin();
    $id = (int) ($b['id'] ?? 0);
    if (!$id) resp(false, ['message' => 'id প্রয়োজন।']);

    // ছবি থাকলে মুছে ফেলুন
    $st = db()->prepare('SELECT image_path FROM posts WHERE id = ? LIMIT 1');
    $st->execute([$id]);
    $row = $st->fetch();
    if ($row && $row['image_path']) {
        $file = __DIR__ . $row['image_path'];
        if (file_exists($file)) @unlink($file);
    }

    db()->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
    resp(true);
}

/* ── ADMIN USERS ──────────────────────────────────── */

/**
 * admin_get_users
 * Input: { search? }
 * Return: { success, users }
 */
function actionAdminGetUsers(array $b): never {
    requireAdmin();
    $search = trim($b['search'] ?? '');

    if ($search) {
        $like = '%' . $search . '%';
        $st   = db()->prepare(
            'SELECT id, name, mobile, address, expiry_date, created_at
               FROM users
              WHERE name LIKE ? OR mobile LIKE ? OR address LIKE ?
              ORDER BY created_at DESC'
        );
        $st->execute([$like, $like, $like]);
    } else {
        $st = db()->query(
            'SELECT id, name, mobile, address, expiry_date, created_at
               FROM users
              ORDER BY created_at DESC'
        );
    }

    resp(true, ['users' => $st->fetchAll()]);
}

/**
 * admin_create_user
 * Input: { name, mobile, address, password, expiry_date }
 */
function actionAdminCreateUser(array $b): never {
    requireAdmin();
    $name     = trim($b['name']        ?? '');
    $mobile   = trim($b['mobile']      ?? '');
    $address  = trim($b['address']     ?? '');
    $password = trim($b['password']    ?? '');
    $expiry   = trim($b['expiry_date'] ?? '');

    if (!$name || !$mobile || !$address || !$password || !$expiry) {
        resp(false, ['message' => 'সকল তথ্য পূরণ করুন।']);
    }

    // Duplicate check
    $ck = db()->prepare('SELECT id FROM users WHERE mobile = ? LIMIT 1');
    $ck->execute([$mobile]);
    if ($ck->fetch()) {
        resp(false, ['message' => 'এই মোবাইলে আগেই অ্যাকাউন্ট আছে।']);
    }

    $ins = db()->prepare(
        'INSERT INTO users (name, mobile, address, password, expiry_date, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())'
    );
    $ins->execute([$name, $mobile, $address, password_hash($password, PASSWORD_DEFAULT), $expiry]);
    $id = db()->lastInsertId();

    resp(true, ['id' => $id]);
}

/**
 * admin_edit_user
 * Input: { id, name, mobile, address, expiry_date, password? }
 */
function actionAdminEditUser(array $b): never {
    requireAdmin();
    $id      = (int)   ($b['id']          ?? 0);
    $name    = trim($b['name']            ?? '');
    $mobile  = trim($b['mobile']          ?? '');
    $address = trim($b['address']         ?? '');
    $expiry  = trim($b['expiry_date']     ?? '');
    $pass    = trim($b['password']        ?? '');

    if (!$id || !$name || !$mobile || !$address || !$expiry) {
        resp(false, ['message' => 'সকল তথ্য পূরণ করুন।']);
    }

    if ($pass) {
        // পাসওয়ার্ড সহ update
        $up = db()->prepare(
            'UPDATE users SET name=?, mobile=?, address=?, expiry_date=?, password=? WHERE id=?'
        );
        $up->execute([$name, $mobile, $address, $expiry, password_hash($pass, PASSWORD_DEFAULT), $id]);
    } else {
        $up = db()->prepare(
            'UPDATE users SET name=?, mobile=?, address=?, expiry_date=? WHERE id=?'
        );
        $up->execute([$name, $mobile, $address, $expiry, $id]);
    }

    resp(true);
}

/**
 * admin_delete_user
 * Input: { id }
 */
function actionAdminDeleteUser(array $b): never {
    requireAdmin();
    $id = (int) ($b['id'] ?? 0);
    if (!$id) resp(false, ['message' => 'id প্রয়োজন।']);
    db()->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    resp(true);
}

/**
 * admin_extend_expiry
 * Input: { id, days?, expiry_date? }
 */
function actionAdminExtendExpiry(array $b): never {
    requireAdmin();
    $id   = (int) ($b['id']   ?? 0);
    $days = (int) ($b['days'] ?? 0);
    $date = trim($b['expiry_date'] ?? '');

    if (!$id) resp(false, ['message' => 'id প্রয়োজন।']);
    if (!$days && !$date) resp(false, ['message' => 'দিন অথবা তারিখ দিন।']);

    if ($date) {
        // নির্দিষ্ট তারিখ সেট
        $up = db()->prepare('UPDATE users SET expiry_date = ? WHERE id = ?');
        $up->execute([$date, $id]);
    } else {
        // বর্তমান expiry_date থেকে X দিন যোগ
        $up = db()->prepare(
            'UPDATE users
                SET expiry_date = DATE_ADD(
                    GREATEST(expiry_date, CURDATE()),
                    INTERVAL ? DAY
                )
              WHERE id = ?'
        );
        $up->execute([$days, $id]);
    }

    resp(true);
}

/* ── JOB POST ─────────────────────────────────────── */

/**
 * submit_job_post
 * Input: { notice, mobile }
 * jobs table-এ save করে (নিচে SQL দেওয়া আছে)
 */
function actionSubmitJobPost(array $b): never {
    $notice = trim($b['notice'] ?? '');
    $mobile = trim($b['mobile'] ?? '');

    if (!$notice || strlen($notice) < 30) {
        resp(false, ['message' => 'নিয়োগ বিজ্ঞপ্তি কমপক্ষে ৩০ অক্ষর হতে হবে।']);
    }
    if (!preg_match('/^(?:\+?88)?0[13-9]\d{8}$/', $mobile)) {
        resp(false, ['message' => 'সঠিক বাংলাদেশী মোবাইল নম্বর দিন।']);
    }

    $ins = db()->prepare(
        'INSERT INTO job_posts (notice, mobile, created_at) VALUES (?, ?, NOW())'
    );
    $ins->execute([$notice, $mobile]);

    resp(true, ['message' => 'নিয়োগ বিজ্ঞপ্তি সফলভাবে জমা হয়েছে!']);
}

/* ──────────────────────────────────────────────────
   9. GUARD — admin session check
   ────────────────────────────────────────────────── */
function requireAdmin(): void {
    if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        resp(false, ['message' => 'এডমিন লগইন প্রয়োজন।'], 403);
    }
}

/*
 ═══════════════════════════════════════════════════
  MySQL SETUP SCRIPT  (একবার run করুন)
  SSH-এ: mysql -u root -p khedmotdb < setup.sql
 ═══════════════════════════════════════════════════

-- users table
CREATE TABLE IF NOT EXISTS users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name         VARCHAR(120)  NOT NULL,
    mobile       VARCHAR(20)   NOT NULL UNIQUE,
    address      VARCHAR(255)  NOT NULL,
    password     VARCHAR(255)  NOT NULL,
    expiry_date  DATE          NOT NULL,
    created_at   DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- posts table
CREATE TABLE IF NOT EXISTS posts (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    content     TEXT          NOT NULL,
    image_path  VARCHAR(255)  DEFAULT NULL,
    created_at  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- job_posts table
CREATE TABLE IF NOT EXISTS job_posts (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    notice     TEXT         NOT NULL,
    mobile     VARCHAR(20)  NOT NULL,
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

*/
