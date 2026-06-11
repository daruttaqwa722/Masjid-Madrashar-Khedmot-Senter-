<?php
/* ══════════════════════════════════════════════════════════════
   মসজিদ-মাদ্রাসার খেদমত সেন্টার — API
   Actions: admin_login, check_session, logout,
            get_public_news, get_user_dashboard,
            user_login, register,
            admin_get_posts, admin_create_post, admin_edit_post, admin_delete_post,
            admin_get_users, admin_create_user, admin_edit_user, admin_delete_user,
            admin_extend_expiry,
            submit_job_post
   ══════════════════════════════════════════════════════════════ */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

// ── DB ────────────────────────────────────────────────────────
$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB error']);
    exit();
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? $_GET['action'] ?? '';

// ── ফোন নম্বর mask ───────────────────────────────────────────
function maskPhone($text) {
    // English digits: 01XXXXXXXXX → 01XXXXXX***
    $text = preg_replace_callback(
        '/(\+?880|0)(1[3-9])(\d{2})([\s\-]?)(\d{3})([\s\-]?)(\d{3})/u',
        function($m) {
            return $m[1].$m[2].$m[3].$m[4].$m[5].$m[6].'***';
        },
        $text
    );
    // Bengali digits
    $bengali = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $english = ['0','1','2','3','4','5','6','7','8','9'];
    $text_en = str_replace($bengali, $english, $text);
    if ($text_en !== $text) {
        $masked_en = maskPhone($text_en);
        $text = str_replace($english, $bengali, $masked_en);
    }
    return $text;
}

// ── পোস্ট row format ──────────────────────────────────────────
function formatPost($row, $mask = false) {
    $content = $row['text'] ?? '';
    if ($mask) $content = maskPhone($content);
    return [
        'id'         => $row['id'],
        'content'    => $content,
        'author'     => $row['author'] ?? '',
        'cats'       => is_string($row['cats']) ? json_decode($row['cats'], true) : ($row['cats'] ?? []),
        'mainCat'    => $row['mainCat'] ?? $row['main_cat'] ?? '',
        'subCat'     => $row['subCat'] ?? $row['sub_cat'] ?? '',
        'image_path' => $row['imgUrl'] ?? $row['image_path'] ?? '',
        'likes'      => (int)($row['likes'] ?? 0),
        'views'      => (int)($row['views'] ?? 0),
        'created_at' => $row['timeStr'] ?? $row['created_at'] ?? '',
        'created'    => (int)($row['created'] ?? 0),
        'hasNumber'  => (bool)($row['hasNumber'] ?? false),
    ];
}

// ══════════════════════════════════════════════════════════════
// ADMIN LOGIN
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_login') {
    $email = $body['email'] ?? '';
    $pass  = $body['password'] ?? '';
    $stmt  = $db->prepare("SELECT * FROM admins WHERE email=? LIMIT 1");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।']);
        exit();
    }
    echo json_encode(['success' => true, 'role' => 'admin', 'token' => 'admin_session']);
    exit();
}

// ══════════════════════════════════════════════════════════════
// CHECK SESSION
// ══════════════════════════════════════════════════════════════
if ($action === 'check_session') {
    $token = $body['token'] ?? '';
    if ($token === 'admin_session') {
        echo json_encode(['success' => true, 'role' => 'admin']);
        exit();
    }
    echo json_encode(['success' => false]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// LOGOUT
// ══════════════════════════════════════════════════════════════
if ($action === 'logout') {
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// USER LOGIN
// ══════════════════════════════════════════════════════════════
if ($action === 'user_login') {
    $mobile = $body['mobile'] ?? '';
    $pass   = $body['password'] ?? '';
    if (!$mobile || !$pass) {
        echo json_encode(['success' => false, 'message' => 'মোবাইল ও পাসওয়ার্ড দিন।']);
        exit();
    }
    $stmt = $db->prepare("SELECT * FROM users WHERE mobile=? LIMIT 1");
    $stmt->bind_param('s', $mobile);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'মোবাইল বা পাসওয়ার্ড সঠিক নয়।']);
        exit();
    }
    $now = time() * 1000;
    if ($user['expiresAt'] && $user['expiresAt'] < $now) {
        echo json_encode(['success' => false, 'message' => 'আপনার মেয়াদ শেষ হয়েছে।']);
        exit();
    }
    unset($user['password']);
    echo json_encode(['success' => true, 'user' => $user]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// REGISTER
// ══════════════════════════════════════════════════════════════
if ($action === 'register') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    $pass    = $body['password'] ?? '';
    if (!$name || !$mobile || !$pass) {
        echo json_encode(['success' => false, 'message' => 'সব তথ্য পূরণ করুন।']);
        exit();
    }
    $check = $db->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
    $check->bind_param('s', $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বর আগেই নিবন্ধিত।']);
        exit();
    }
    $id        = uniqid('u_', true);
    $hash      = password_hash($pass, PASSWORD_DEFAULT);
    $createdAt = time() * 1000;
    $expiresAt = $createdAt + (30 * 86400 * 1000); // ৩০ দিন
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, address, password, role, createdAt, expiresAt) VALUES (?,?,?,?,?,'user',?,?)");
    $stmt->bind_param('sssssii', $id, $name, $mobile, $address, $hash, $createdAt, $expiresAt);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// GET PUBLIC NEWS (লগইন ছাড়া — masked)
// ══════════════════════════════════════════════════════════════
if ($action === 'get_public_news') {
    $rows = $db->query("SELECT * FROM posts ORDER BY created DESC LIMIT 100")->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, true), $rows);
    echo json_encode(['success' => true, 'posts' => $posts, 'masked' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// GET USER DASHBOARD (লগইন করা user — full number)
// ══════════════════════════════════════════════════════════════
if ($action === 'get_user_dashboard') {
    $mobile = $body['mobile'] ?? '';
    $stmt   = $db->prepare("SELECT * FROM users WHERE mobile=? LIMIT 1");
    $stmt->bind_param('s', $mobile);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user) { echo json_encode(['success' => false]); exit(); }
    unset($user['password']);
    $now       = time() * 1000;
    $days_left = $user['expiresAt'] ? (int)(($user['expiresAt'] - $now) / 86400000) : 0;
    $is_active = $days_left >= 0;
    $rows  = $db->query("SELECT * FROM posts ORDER BY created DESC LIMIT 50")->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows); // full number
    echo json_encode(['success' => true, 'user' => $user, 'days_left' => $days_left, 'is_active' => $is_active, 'posts' => $posts]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — GET POSTS (full number)
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_get_posts') {
    $rows  = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'posts' => $posts]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — CREATE POST
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_create_post') {
    $id      = uniqid('p_', true);
    $text    = $body['content'] ?? '';
    $author  = $body['author'] ?? 'Admin';
    $cats    = json_encode($body['cats'] ?? []);
    $mainCat = $body['mainCat'] ?? '';
    $subCat  = $body['subCat'] ?? '';
    $imgUrl  = $body['image_base64'] ?? '';
    $hasNum  = (int)($body['hasNumber'] ?? 0);
    $created = time() * 1000;
    $timeStr = date('d/m/Y');
    if (!$text) { echo json_encode(['success' => false, 'message' => 'পোস্ট লিখুন।']); exit(); }
    // ডুপ্লিকেট চেক
    $dup = $db->prepare("SELECT id FROM posts WHERE text=? LIMIT 1");
    $dup->bind_param('s', $text);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে! ডুপ্লিকেট পোস্ট করা যাবে না।']);
        exit();
    }
    $stmt = $db->prepare("INSERT INTO posts (id, text, author, cats, mainCat, subCat, created, timeStr, likes, views, imgUrl, hasNumber) VALUES (?,?,?,?,?,?,?,?,0,0,?,?)");
    $stmt->bind_param('ssssssissi', $id, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $id]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — EDIT POST
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_edit_post') {
    $id   = $body['id'] ?? '';
    $text = $body['content'] ?? '';
    if (!$id || !$text) { echo json_encode(['success' => false, 'message' => 'id ও content প্রয়োজন']); exit(); }
    $stmt = $db->prepare("UPDATE posts SET text=? WHERE id=?");
    $stmt->bind_param('ss', $text, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — DELETE POST
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_delete_post') {
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM posts WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — GET USERS
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_get_users') {
    $search = $body['search'] ?? '';
    if ($search) {
        $s = "%$search%";
        $stmt = $db->prepare("SELECT id, name, mobile, address, role, createdAt, expiresAt FROM users WHERE mobile LIKE ? OR name LIKE ? ORDER BY createdAt DESC");
        $stmt->bind_param('ss', $s, $s);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $rows = $db->query("SELECT id, name, mobile, address, role, createdAt, expiresAt FROM users ORDER BY createdAt DESC")->fetch_all(MYSQLI_ASSOC);
    }
    foreach ($rows as &$row) {
        $row['expiry_date'] = $row['expiresAt'] ? date('d/m/Y', intval($row['expiresAt']) / 1000) : '';
    }
    echo json_encode(['success' => true, 'users' => $rows]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — CREATE USER
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_create_user') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    $pass    = $body['password'] ?? '';
    $expiry  = $body['expiry_date'] ?? '';
    if (!$name || !$mobile || !$pass || !$expiry) {
        echo json_encode(['success' => false, 'message' => 'সব তথ্য পূরণ করুন।']);
        exit();
    }
    $check = $db->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
    $check->bind_param('s', $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বর আগেই আছে।']);
        exit();
    }
    $id        = uniqid('u_', true);
    $hash      = password_hash($pass, PASSWORD_DEFAULT);
    $expiresAt = strtotime(str_replace('/', '-', $expiry)) * 1000;
    $createdAt = time() * 1000;
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, address, password, role, createdAt, expiresAt) VALUES (?,?,?,?,?,'user',?,?)");
    $stmt->bind_param('sssssii', $id, $name, $mobile, $address, $hash, $createdAt, $expiresAt);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — EDIT USER
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_edit_user') {
    $id      = $body['id'] ?? '';
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    $pass    = $body['password'] ?? '';
    $expiry  = $body['expiry_date'] ?? $body['expiry'] ?? '';
    $expiresAt = $expiry ? strtotime(str_replace('/', '-', $expiry)) * 1000 : 0;
    if ($pass) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, address=?, password=?, expiresAt=? WHERE id=?");
        $stmt->bind_param('ssssis', $name, $mobile, $address, $hash, $expiresAt, $id);
    } else {
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, address=?, expiresAt=? WHERE id=?");
        $stmt->bind_param('sssis', $name, $mobile, $address, $expiresAt, $id);
    }
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — DELETE USER
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_delete_user') {
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ADMIN — EXTEND EXPIRY
// ══════════════════════════════════════════════════════════════
if ($action === 'admin_extend_expiry') {
    $id   = $body['id'] ?? '';
    $days = intval($body['days'] ?? 0);
    $date = $body['expiry_date'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    if ($date) {
        $expiresAt = strtotime(str_replace('/', '-', $date)) * 1000;
    } elseif ($days > 0) {
        $curr = $db->prepare("SELECT expiresAt FROM users WHERE id=? LIMIT 1");
        $curr->bind_param('s', $id);
        $curr->execute();
        $row = $curr->get_result()->fetch_assoc();
        $base = $row['expiresAt'] ?? time() * 1000;
        $expiresAt = $base + ($days * 86400 * 1000);
    } else {
        echo json_encode(['success' => false, 'message' => 'দিন অথবা তারিখ দিন।']);
        exit();
    }
    $stmt = $db->prepare("UPDATE users SET expiresAt=? WHERE id=?");
    $stmt->bind_param('is', $expiresAt, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// SUBMIT JOB POST (pending)
// ══════════════════════════════════════════════════════════════
if ($action === 'submit_job_post') {
    $id      = uniqid('pnd_', true);
    $text    = $body['content'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $name    = $body['name'] ?? '';
    $created = time() * 1000;
    if (!$text) { echo json_encode(['success' => false, 'message' => 'পোস্ট লিখুন।']); exit(); }
    $stmt = $db->prepare("INSERT INTO pending_posts (id, text, mobile, name, created) VALUES (?,?,?,?,?)");
    $stmt->bind_param('ssssi', $id, $text, $mobile, $name, $created);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ══════════════════════════════════════════════════════════════
// ROUTER — legacy REST support
// ══════════════════════════════════════════════════════════════
$r      = $_GET['r'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

switch ($r) {
    case 'posts':
        if ($method === 'GET') {
            $limit  = intval($_GET['limit'] ?? 50);
            $offset = intval($_GET['offset'] ?? 0);
            $cat    = $_GET['cat'] ?? '';
            if ($cat) {
                $stmt = $db->prepare("SELECT * FROM posts WHERE mainCat=? ORDER BY created DESC LIMIT ? OFFSET ?");
                $stmt->bind_param('sii', $cat, $limit, $offset);
            } else {
                $stmt = $db->prepare("SELECT * FROM posts ORDER BY created DESC LIMIT ? OFFSET ?");
                $stmt->bind_param('ii', $limit, $offset);
            }
            $stmt->execute();
            $rows  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $posts = array_map(fn($r) => formatPost($r, true), $rows);
            echo json_encode(['success' => true, 'data' => $posts]);
        }
        break;

    case 'like':
        $id = $_GET['id'] ?? $body['id'] ?? '';
        if ($id) {
            $db->query("UPDATE posts SET likes=likes+1 WHERE id='".$db->real_escape_string($id)."'");
        }
        echo json_encode(['success' => true]);
        break;

    case 'visitors':
        if ($method === 'POST') {
            $date    = date('Y-m-d');
            $sources = json_encode($body['sources'] ?? []);
            $pages   = json_encode($body['pages'] ?? []);
            $newRet  = json_encode($body['newReturning'] ?? []);
            $cats    = json_encode($body['categories'] ?? []);
            $stmt = $db->prepare("INSERT INTO visitors (id,count,sources,pages,newReturning,categories) VALUES (?,1,?,?,?,?) ON DUPLICATE KEY UPDATE count=count+1, sources=?, pages=?, newReturning=?, categories=?");
            $stmt->bind_param('sssssssss', $date, $sources, $pages, $newRet, $cats, $sources, $pages, $newRet, $cats);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    case 'testimonials':
        if ($method === 'GET') {
            $rows = $db->query("SELECT * FROM testimonials ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
        }
        break;

    default:
        if (!$action) echo json_encode(['error' => 'invalid route']);
        break;
}

$db->close();
?>
