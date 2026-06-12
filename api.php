<?php
/* ══════════════════════════════════════════════════════════════
   মসজিদ-মাদ্রাসার খেদমত সেন্টার — API v2
   ══════════════════════════════════════════════════════════════ */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit(); }

$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) { echo json_encode(['success' => false, 'message' => 'DB error']); exit(); }

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? $_GET['action'] ?? '';

function maskPhone($text) {
    $text = preg_replace_callback(
        '/(\+?880|0)(1[3-9])(\d{2})([\s\-]?)(\d{3})([\s\-]?)(\d{3})/u',
        function($m) { return $m[1].$m[2].$m[3].$m[4].$m[5].$m[6].'***'; },
        $text
    );
    $bengali = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $english = ['0','1','2','3','4','5','6','7','8','9'];
    $text_en = str_replace($bengali, $english, $text);
    if ($text_en !== $text) {
        $masked_en = maskPhone($text_en);
        $text = str_replace($english, $bengali, $masked_en);
    }
    return $text;
}

function formatPost($row, $mask = false) {
    $content = $row['text'] ?? '';
    if ($mask) $content = maskPhone($content);
    return [
        'id'         => $row['id'],
        'content'    => $content,
        'title'      => $row['title'] ?? '',
        'slug'       => $row['slug'] ?? $row['id'],
        'category'   => $row['category'] ?? '',
        'meta_title' => $row['meta_title'] ?? '',
        'meta_desc'  => $row['meta_desc'] ?? '',
        'author'     => $row['author'] ?? '',
        'cats'       => is_string($row['cats']) ? json_decode($row['cats'], true) : ($row['cats'] ?? []),
        'mainCat'    => $row['mainCat'] ?? '',
        'subCat'     => $row['subCat'] ?? '',
        'image_path' => $row['imgUrl'] ?? $row['image_path'] ?? '',
        'likes'      => (int)($row['likes'] ?? 0),
        'views'      => (int)($row['views'] ?? 0),
        'created_at' => $row['timeStr'] ?? '',
        'created'    => (int)($row['created'] ?? 0),
        'hasNumber'  => (bool)($row['hasNumber'] ?? false),
        'status'     => $row['status'] ?? 'active',
    ];
}

function getCatName($cat) {
    $names = [
        'mosque'         => 'মসজিদের নিয়োগ বিজ্ঞপ্তি',
        'male-madrasa'   => 'পুরুষ মাদ্রাসার নিয়োগ',
        'female-madrasa' => 'মহিলা মাদ্রাসার নিয়োগ',
    ];
    return $names[$cat] ?? 'নিয়োগ বিজ্ঞপ্তি';
}

function autoSEO($text, $category, $id) {
    $catName    = getCatName($category);
    $first_line = explode("\n", trim($text))[0];
    $title      = mb_substr($first_line, 0, 55) . ' | ' . $catName;
    $slug       = $id;
    $meta_title = mb_substr($title, 0, 60) . ' | খেদমত সেন্টার';
    $clean      = preg_replace('/\s+/', ' ', strip_tags($text));
    $meta_desc  = mb_substr($clean, 0, 150) . '...';
    return [$title, $slug, $meta_title, $meta_desc];
}

// ADMIN LOGIN
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

// CHECK SESSION
if ($action === 'check_session') {
    $token = $body['token'] ?? '';
    if ($token === 'admin_session') {
        echo json_encode(['success' => true, 'role' => 'admin']);
        exit();
    }
    echo json_encode(['success' => false]);
    exit();
}

// LOGOUT
if ($action === 'logout') {
    echo json_encode(['success' => true]);
    exit();
}

// USER LOGIN
if ($action === 'user_login') {
    $mobile = $body['mobile'] ?? '';
    $pass   = $body['password'] ?? '';
    if (!$mobile || !$pass) { echo json_encode(['success' => false, 'message' => 'মোবাইল ও পাসওয়ার্ড দিন।']); exit(); }
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

// REGISTER
if ($action === 'register') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    $pass    = $body['password'] ?? '';
    if (!$name || !$mobile || !$pass) { echo json_encode(['success' => false, 'message' => 'সব তথ্য পূরণ করুন।']); exit(); }
    $check = $db->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
    $check->bind_param('s', $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) { echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বর আগেই নিবন্ধিত।']); exit(); }
    $id        = uniqid('u_', true);
    $hash      = password_hash($pass, PASSWORD_DEFAULT);
    $createdAt = time() * 1000;
    $expiresAt = $createdAt + (30 * 86400 * 1000);
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, address, password, role, createdAt, expiresAt) VALUES (?,?,?,?,?,'user',?,?)");
    $stmt->bind_param('sssssii', $id, $name, $mobile, $address, $hash, $createdAt, $expiresAt);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// GET PUBLIC NEWS (masked)
if ($action === 'get_public_news') {
    $cat    = $body['category'] ?? $_GET['category'] ?? '';
    $hours  = intval($body['hours'] ?? $_GET['hours'] ?? 0);
    $limit  = intval($body['limit'] ?? 50);
    $offset = intval($body['offset'] ?? 0);
    $where  = "WHERE status='active'";
    $params = [];
    $types  = '';
    if ($cat) { $where .= " AND category=?"; $params[] = $cat; $types .= 's'; }
    if ($hours > 0) { $since = (time() - ($hours * 3600)) * 1000; $where .= " AND created >= ?"; $params[] = $since; $types .= 'i'; }
    $params[] = $limit;
    $params[] = $offset;
    $types   .= 'ii';
    $stmt = $db->prepare("SELECT * FROM posts $where ORDER BY created DESC LIMIT ? OFFSET ?");
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $rows  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, true), $rows);
    echo json_encode(['success' => true, 'posts' => $posts, 'masked' => true]);
    exit();
}

// GET USER DASHBOARD (full number)
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
    $cat    = $body['category'] ?? '';
    $hours  = intval($body['hours'] ?? 0);
    $where  = "WHERE status='active'";
    $params = [];
    $types  = '';
    if ($cat) { $where .= " AND category=?"; $params[] = $cat; $types .= 's'; }
    if ($hours > 0) { $since = (time() - ($hours * 3600)) * 1000; $where .= " AND created >= ?"; $params[] = $since; $types .= 'i'; }
    $params[] = 50;
    $types   .= 'i';
    $stmt2 = $db->prepare("SELECT * FROM posts $where ORDER BY created DESC LIMIT ?");
    $stmt2->bind_param($types, ...$params);
    $stmt2->execute();
    $rows  = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'user' => $user, 'days_left' => $days_left, 'is_active' => $is_active, 'posts' => $posts]);
    exit();
}

// ADMIN — GET POSTS
if ($action === 'admin_get_posts') {
    $rows  = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'posts' => $posts]);
    exit();
}

// ADMIN — CREATE POST
if ($action === 'admin_create_post') {
    $id       = uniqid('p_', true);
    $text     = $body['content'] ?? '';
    $author   = $body['author'] ?? 'Admin';
    $cats     = json_encode($body['cats'] ?? []);
    $mainCat  = $body['mainCat'] ?? '';
    $subCat   = $body['subCat'] ?? '';
    $imgUrl   = $body['image_base64'] ?? '';
    $hasNum   = (int)($body['hasNumber'] ?? 0);
    $category = $body['category'] ?? 'mosque';
    $created  = time() * 1000;
    $timeStr  = date('d/m/Y');
    if (!$text) { echo json_encode(['success' => false, 'message' => 'পোস্ট লিখুন।']); exit(); }
    $dup = $db->prepare("SELECT id FROM posts WHERE text=? LIMIT 1");
    $dup->bind_param('s', $text);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) { echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে!']); exit(); }
    $title      = $body['title'] ?? '';
    $slug       = $body['slug'] ?? '';
    $meta_title = $body['meta_title'] ?? '';
    $meta_desc  = $body['meta_desc'] ?? '';
    list($autoTitle, $autoSlug, $autoMeta, $autoDesc) = autoSEO($text, $category, $id);
    if (!$title)      $title      = $autoTitle;
    if (!$slug)       $slug       = $autoSlug;
    if (!$meta_title) $meta_title = $autoMeta;
    if (!$meta_desc)  $meta_desc  = $autoDesc;
    $stmt = $db->prepare("INSERT INTO posts (id, text, author, cats, mainCat, subCat, created, timeStr, likes, views, imgUrl, hasNumber, title, slug, category, meta_title, meta_desc) VALUES (?,?,?,?,?,?,?,?,0,0,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssisssssss', $id, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum, $title, $slug, $category, $meta_title, $meta_desc);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $id]);
    exit();
}

// ADMIN — EDIT POST
if ($action === 'admin_edit_post') {
    $id       = $body['id'] ?? '';
    $text     = $body['content'] ?? '';
    $category = $body['category'] ?? '';
    if (!$id || !$text) { echo json_encode(['success' => false, 'message' => 'id ও content প্রয়োজন']); exit(); }
    $title      = $body['title'] ?? '';
    $meta_title = $body['meta_title'] ?? '';
    $meta_desc  = $body['meta_desc'] ?? '';
    if (!$title || !$meta_title || !$meta_desc) {
        list($autoTitle, $autoSlug, $autoMeta, $autoDesc) = autoSEO($text, $category, $id);
        if (!$title)      $title      = $autoTitle;
        if (!$meta_title) $meta_title = $autoMeta;
        if (!$meta_desc)  $meta_desc  = $autoDesc;
    }
    $stmt = $db->prepare("UPDATE posts SET text=?, title=?, category=?, meta_title=?, meta_desc=? WHERE id=?");
    $stmt->bind_param('ssssss', $text, $title, $category, $meta_title, $meta_desc, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ADMIN — DELETE POST
if ($action === 'admin_delete_post') {
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM posts WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ADMIN — GET USERS
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

// ADMIN — CREATE USER
if ($action === 'admin_create_user') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    $pass    = $body['password'] ?? '';
    $expiry  = $body['expiry_date'] ?? '';
    if (!$name || !$mobile || !$pass || !$expiry) { echo json_encode(['success' => false, 'message' => 'সব তথ্য পূরণ করুন।']); exit(); }
    $check = $db->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
    $check->bind_param('s', $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) { echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বর আগেই আছে।']); exit(); }
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

// ADMIN — EDIT USER
if ($action === 'admin_edit_user') {
    $id        = $body['id'] ?? '';
    $name      = $body['name'] ?? '';
    $mobile    = $body['mobile'] ?? '';
    $address   = $body['address'] ?? '';
    $pass      = $body['password'] ?? '';
    $expiry    = $body['expiry_date'] ?? $body['expiry'] ?? '';
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

// ADMIN — DELETE USER
if ($action === 'admin_delete_user') {
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// ADMIN — EXTEND EXPIRY
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
        $row       = $curr->get_result()->fetch_assoc();
        $base      = $row['expiresAt'] ?? time() * 1000;
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

// SUBMIT JOB POST (pending)
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

// GET CATEGORY PAGE DATA
if ($action === 'get_category') {
    $slug   = $body['slug'] ?? $_GET['slug'] ?? '';
    $page   = intval($body['page'] ?? $_GET['page'] ?? 1);
    $limit  = 20;
    $offset = ($page - 1) * $limit;
    $cat    = $db->prepare("SELECT * FROM categories WHERE slug=? LIMIT 1");
    $cat->bind_param('s', $slug);
    $cat->execute();
    $category = $cat->get_result()->fetch_assoc();
    if (!$category) { echo json_encode(['success' => false, 'message' => 'Category not found']); exit(); }
    $catMap = ['mosque-jobs' => 'mosque', 'male-madrasa-jobs' => 'male-madrasa', 'female-madrasa-jobs' => 'female-madrasa'];
    $catVal = $catMap[$slug] ?? $slug;
    $total_stmt = $db->prepare("SELECT COUNT(*) as cnt FROM posts WHERE category=? AND status='active'");
    $total_stmt->bind_param('s', $catVal);
    $total_stmt->execute();
    $total = $total_stmt->get_result()->fetch_assoc()['cnt'];
    $posts_stmt = $db->prepare("SELECT * FROM posts WHERE category=? AND status='active' ORDER BY created DESC LIMIT ? OFFSET ?");
    $posts_stmt->bind_param('sii', $catVal, $limit, $offset);
    $posts_stmt->execute();
    $rows  = $posts_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, true), $rows);
    echo json_encode(['success' => true, 'category' => $category, 'posts' => $posts, 'total' => $total, 'page' => $page, 'totalPages' => ceil($total / $limit)]);
    exit();
}

// ROUTER
$r      = $_GET['r'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
switch ($r) {
    case 'posts':
        if ($method === 'GET') {
            $limit  = intval($_GET['limit'] ?? 50);
            $offset = intval($_GET['offset'] ?? 0);
            $cat    = $_GET['cat'] ?? '';
            if ($cat) {
                $stmt = $db->prepare("SELECT * FROM posts WHERE mainCat=? AND status='active' ORDER BY created DESC LIMIT ? OFFSET ?");
                $stmt->bind_param('sii', $cat, $limit, $offset);
            } else {
                $stmt = $db->prepare("SELECT * FROM posts WHERE status='active' ORDER BY created DESC LIMIT ? OFFSET ?");
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
        if ($id) { $db->query("UPDATE posts SET likes=likes+1 WHERE id='".$db->real_escape_string($id)."'"); }
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
