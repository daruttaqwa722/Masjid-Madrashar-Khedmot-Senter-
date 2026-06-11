<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// DB Connection
$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit();
}

$r = $_GET['r'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];
$body = json_decode(file_get_contents('php://input'), true) ?? [];

// action parameter দিয়ে POST request handle
$action = $body['action'] ?? '';
if ($action === 'admin_login') {
    $email = $body['email'] ?? '';
    $pass  = $body['password'] ?? '';

    if (!$email || !$pass) {
        echo json_encode(['success' => false, 'message' => 'ইমেইল ও পাসওয়ার্ড প্রয়োজন।']);
        exit();
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'ইমেইল বা পাসওয়ার্ড সঠিক নয়।']);
        exit();
    }

    unset($user['password']);
    echo json_encode(['success' => true, 'data' => $user]);
    exit();
}
if ($action === 'check_session') {
    $token = $body['token'] ?? '';
    if ($token === 'admin_session') {
        echo json_encode(['success' => true, 'role' => 'admin']);
        exit();
    }
    echo json_encode(['success' => false]);
    exit();
    if (false) {
        echo json_encode(['success' => true, 'role' => $user['role'], 'data' => $user]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
}


if ($action === 'login' || $action === 'user_login') {
    $mobile = $body['mobile'] ?? '';
    $pass   = $body['password'] ?? '';
    if (!$mobile || !$pass) {
        echo json_encode(['success' => false, 'message' => 'মোবাইল ও পাসওয়ার্ড দিন।']);
        exit();
    }
    $stmt = $db->prepare("SELECT * FROM users WHERE mobile=?");
    $stmt->bind_param('s', $mobile);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if (!$user || !password_verify($pass, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'মোবাইল বা পাসওয়ার্ড ভুল।']);
        exit();
    }
    $now = time() * 1000;
    if ($user['expiresAt'] < $now) {
        echo json_encode(['success' => false, 'message' => 'আপনার মেয়াদ শেষ হয়েছে।']);
        exit();
    }
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['role'] = $user['role'];
    unset($user['password']);
    echo json_encode(['success' => true, 'role' => $user['role'], 'data' => $user]);
    exit();
}

if ($action === 'admin_create_post') {
    $id      = uniqid('p_', true);
    $text    = $body['content'] ?? '';
    $author  = $body['author'] ?? 'Admin';
    $cats    = json_encode($body['cats'] ?? []);
    $mainCat = $body['mainCat'] ?? '';
    $subCat  = $body['subCat'] ?? '';
    $created = time() * 1000;
    $timeStr = date('d M Y');
    $imgUrl  = $body['image_base64'] ?? '';
    $hasNum  = intval($body['hasNumber'] ?? 0);
    $stmt = $db->prepare("INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber) VALUES (?,?,?,?,?,?,?,?,0,0,?,?)");
    $stmt->bind_param('ssssssissi', $id, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum);
    $stmt->execute();
    echo json_encode(['success' => true, 'id' => $id]);
    exit();
}


if ($action === 'get_public_news') {
    $rows = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as &$row) {
        $row['cats'] = json_decode($row['cats'], true);
        $row['content'] = preg_replace('/(\d{8})(\d{3})/', '$1***', $row['text']);
        $row['created_at'] = $row['timeStr'];
        $row['image_path'] = $row['imgUrl'];
    }
    echo json_encode(['success' => true, 'posts' => $rows]);
    exit();
}


if ($action === 'admin_create_user') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $pass    = $body['password'] ?? '';
    $expiry  = $body['expiry_date'] ?? $body['expiry'] ?? '';
    if (!$name || !$mobile || !$expiry) {
        echo json_encode(['success' => false, 'message' => 'নাম, মোবাইল ও মেয়াদ আবশ্যক।']);
        exit();
    }
    $stmt = $db->prepare("SELECT id FROM users WHERE mobile=?");
    $stmt->bind_param('s', $mobile);
    $stmt->execute();
    if ($stmt->get_result()->fetch_assoc()) {
        echo json_encode(['success' => false, 'message' => 'এই নাম্বার আগেই নিবন্ধিত।']);
        exit();
    }
    $id = uniqid('u_', true);
    $hash = $pass ? password_hash($pass, PASSWORD_DEFAULT) : null;
    $expiresAt = strtotime(str_replace('/', '-', $expiry)) * 1000;
    $createdAt = time() * 1000;
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, password, role, createdAt, expiresAt) VALUES (?,?,?,?,?,?,?)");
    $role='user';
    $stmt->bind_param('sssssii', $id, $name, $mobile, $hash, $role, $createdAt, $expiresAt);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'admin_edit_user') {
    $id      = $body['id'] ?? '';
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $pass    = $body['password'] ?? '';
    $expiry  = $body['expiry_date'] ?? $body['expiry'] ?? '';
    $expiresAt = strtotime(str_replace('/', '-', $expiry)) * 1000;
    if ($pass) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, password=?, expiresAt=? WHERE id=?");
        $stmt->bind_param('sssis', $name, $mobile, $hash, $expiresAt, $id);
    } else {
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, expiresAt=? WHERE id=?");
        $stmt->bind_param('ssis', $name, $mobile, $expiresAt, $id);
    }
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}


if ($action === 'admin_get_users') {
    $rows = $db->query("SELECT id, name, mobile, role, createdAt, expiresAt FROM users ORDER BY createdAt DESC")->fetch_all(MYSQLI_ASSOC);
    echo json_encode(['success' => true, 'users' => $rows]);
    exit();
}

if ($action === 'admin_delete_user') {
    $id = $body['id'] ?? '';
    $stmt = $db->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

if ($action === 'admin_get_posts') {
    $rows = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as &$row) {
        $row['cats'] = json_decode($row['cats'], true);
        $row['content'] = $row['text'];
        $row['created_at'] = $row['timeStr'];
        $row['image_path'] = $row['imgUrl'];
    }
    echo json_encode(['success' => true, 'posts' => $rows]);
    exit();
}

if ($action === 'admin_edit_post') {
    $id = $body['id'] ?? '';
    $text = $body['content'] ?? '';
    if (!$id || !$text) { echo json_encode(['success' => false, 'message' => 'id ও content প্রয়োজন']); exit(); }
    $stmt = $db->prepare("UPDATE posts SET text=? WHERE id=?");
    $stmt->bind_param('ss', $text, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
if ($action === 'admin_delete_post') {
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM posts WHERE id=?");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
// ─── ROUTER ───────────────────────────────────────────────
switch ($r) {

    // ── POSTS ──────────────────────────────────────────────
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
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as &$row) {
                $row['cats'] = json_decode($row['cats'], true);
            }
            echo json_encode(['success' => true, 'data' => $rows]);
        }

        elseif ($method === 'POST') {
            $id      = uniqid('p_', true);
            $text    = $body['text'] ?? '';
            $author  = $body['author'] ?? '';
            $cats    = json_encode($body['cats'] ?? []);
            $mainCat = $body['mainCat'] ?? '';
            $subCat  = $body['subCat'] ?? '';
            $created = time() * 1000;
            $timeStr = $body['timeStr'] ?? date('d M Y');
            $imgUrl  = $body['imgUrl'] ?? '';
            $hasNum  = intval($body['hasNumber'] ?? 0);

            $stmt = $db->prepare("INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber) VALUES (?,?,?,?,?,?,?,?,0,0,?,?)");
            $stmt->bind_param('ssssssissi', $id, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum);
            $stmt->execute();
            echo json_encode(['success' => true, 'id' => $id]);
        }
        break;

    // ── SINGLE POST ────────────────────────────────────────
    case 'post':
        $id = $_GET['id'] ?? '';
        if (!$id) { echo json_encode(['error' => 'id required']); break; }

        if ($method === 'GET') {
            $stmt = $db->prepare("SELECT * FROM posts WHERE id=?");
            $stmt->bind_param('s', $id);
            $stmt->execute();
            $row = $stmt->get_result()->fetch_assoc();
            if ($row) {
                $row['cats'] = json_decode($row['cats'], true);
                // increment views
                $db->query("UPDATE posts SET views=views+1 WHERE id='".  $db->real_escape_string($id)."'");
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['error' => 'not found']);
            }
        }

        elseif ($method === 'PUT') {
            $fields = [];
            $types  = '';
            $vals   = [];
            foreach (['text','author','mainCat','subCat','timeStr','imgUrl'] as $f) {
                if (isset($body[$f])) {
                    $fields[] = "$f=?";
                    $types   .= 's';
                    $vals[]   = $body[$f];
                }
            }
            if (isset($body['cats'])) {
                $fields[] = "cats=?";
                $types   .= 's';
                $vals[]   = json_encode($body['cats']);
            }
            if (isset($body['hasNumber'])) {
                $fields[] = "hasNumber=?";
                $types   .= 'i';
                $vals[]   = intval($body['hasNumber']);
            }
            if (empty($fields)) { echo json_encode(['error' => 'nothing to update']); break; }
            $types .= 's';
            $vals[] = $id;
            $stmt = $db->prepare("UPDATE posts SET ".implode(',', $fields)." WHERE id=?");
            $stmt->bind_param($types, ...$vals);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }

        elseif ($method === 'DELETE') {
            $stmt = $db->prepare("DELETE FROM posts WHERE id=?");
            $stmt->bind_param('s', $id);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    // ── LIKE POST ──────────────────────────────────────────
    case 'like':
        $id = $_GET['id'] ?? $body['id'] ?? '';
        if (!$id) { echo json_encode(['error' => 'id required']); break; }
        $db->query("UPDATE posts SET likes=likes+1 WHERE id='".$db->real_escape_string($id)."'");
        echo json_encode(['success' => true]);
        break;

    // ── PENDING POSTS ──────────────────────────────────────
    case 'pending':
        if ($method === 'GET') {
            $status = $_GET['status'] ?? 'pending';
            $stmt = $db->prepare("SELECT * FROM pending_posts WHERE status=? ORDER BY created DESC");
            $stmt->bind_param('s', $status);
            $stmt->execute();
            $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as &$row) {
                $row['cats'] = json_decode($row['cats'], true);
            }
            echo json_encode(['success' => true, 'data' => $rows]);
        }

        elseif ($method === 'POST') {
            $id       = uniqid('pp_', true);
            $text     = $body['text'] ?? '';
            $mobile   = $body['mobile'] ?? '';
            $cat      = $body['cat'] ?? '';
            $cats     = json_encode($body['cats'] ?? []);
            $mainCat  = $body['mainCat'] ?? '';
            $catLabel = $body['catLabel'] ?? '';
            $timeStr  = date('d M Y');
            $created  = time() * 1000;

            $stmt = $db->prepare("INSERT INTO pending_posts (id,text,mobile,cat,cats,mainCat,catLabel,timeStr,created,status) VALUES (?,?,?,?,?,?,?,?,?,'pending')");
            $stmt->bind_param('ssssssssi', $id, $text, $mobile, $cat, $cats, $mainCat, $catLabel, $timeStr, $created);
            $stmt->execute();
            echo json_encode(['success' => true, 'id' => $id]);
        }
        break;

    // ── APPROVE PENDING ────────────────────────────────────
    case 'approve':
        $id = $_GET['id'] ?? $body['id'] ?? '';
        if (!$id) { echo json_encode(['error' => 'id required']); break; }

        $stmt = $db->prepare("SELECT * FROM pending_posts WHERE id=?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if (!$row) { echo json_encode(['error' => 'not found']); break; }

        $newId   = uniqid('p_', true);
        $created = time() * 1000;
        $timeStr = date('d M Y');
        $cats    = $row['cats'];
        $imgUrl  = '';
        $hasNum  = 0;

        $stmt2 = $db->prepare("INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber) VALUES (?,?,?,?,?,?,?,?,0,0,?,?)");
        $stmt2->bind_param('ssssssissi', $newId, $row['text'], $row['mobile'], $cats, $row['mainCat'], $row['cat'], $created, $timeStr, $imgUrl, $hasNum);
        $stmt2->execute();

        $stmt3 = $db->prepare("UPDATE pending_posts SET status='approved' WHERE id=?");
        $stmt3->bind_param('s', $id);
        $stmt3->execute();

        echo json_encode(['success' => true, 'postId' => $newId]);
        break;

    // ── REJECT PENDING ─────────────────────────────────────
    case 'reject':
        $id = $_GET['id'] ?? $body['id'] ?? '';
        if (!$id) { echo json_encode(['error' => 'id required']); break; }
        $stmt = $db->prepare("UPDATE pending_posts SET status='rejected' WHERE id=?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        echo json_encode(['success' => true]);
        break;

    // ── REGISTER ───────────────────────────────────────────
    case 'register':
        $name    = $body['name'] ?? '';
        $mobile  = $body['mobile'] ?? '';
        $pass    = $body['password'] ?? '';

        if (!$name || !$mobile || !$pass) {
            echo json_encode(['error' => 'name, mobile, password required']); break;
        }

        $stmt = $db->prepare("SELECT id FROM users WHERE mobile=?");
        $stmt->bind_param('s', $mobile);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode(['error' => 'mobile already registered']); break;
        }

        $id        = uniqid('u_', true);
        $hash      = password_hash($pass, PASSWORD_DEFAULT);
        $createdAt = time() * 1000;
        $expiresAt = ($createdAt) + (365 * 24 * 3600 * 1000);

        $stmt2 = $db->prepare("INSERT INTO users (id,name,mobile,password,createdAt,expiresAt) VALUES (?,?,?,?,?,?)");
        $stmt2->bind_param('ssssii', $id, $name, $mobile, $hash, $createdAt, $expiresAt);
        $stmt2->execute();
        echo json_encode(['success' => true, 'id' => $id, 'name' => $name, 'mobile' => $mobile, 'expiresAt' => $expiresAt]);
        break;

    // ── LOGIN ──────────────────────────────────────────────
    case 'login':
        $mobile = $body['mobile'] ?? '';
        $pass   = $body['password'] ?? '';

        if (!$mobile || !$pass) {
            echo json_encode(['error' => 'mobile and password required']); break;
        }

        $stmt = $db->prepare("SELECT * FROM users WHERE mobile=?");
        $stmt->bind_param('s', $mobile);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($pass, $user['password'])) {
            echo json_encode(['error' => 'invalid mobile or password']); break;
        }

        unset($user['password']);
        echo json_encode(['success' => true, 'data' => $user]);
        break;

    // ── USERS (admin) ──────────────────────────────────────
    case 'users':
        if ($method === 'GET') {
            $rows = $db->query("SELECT id,name,mobile,createdAt,expiresAt FROM users ORDER BY createdAt DESC")->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
        }
        break;

    // ── VISITORS ───────────────────────────────────────────
    case 'visitors':
        if ($method === 'GET') {
            $rows = $db->query("SELECT * FROM visitors ORDER BY id DESC LIMIT 60")->fetch_all(MYSQLI_ASSOC);
            foreach ($rows as &$row) {
                $row['sources']      = json_decode($row['sources'], true);
                $row['pages']        = json_decode($row['pages'], true);
                $row['newReturning'] = json_decode($row['newReturning'], true);
                $row['categories']   = json_decode($row['categories'], true);
            }
            echo json_encode(['success' => true, 'data' => $rows]);
        }

        elseif ($method === 'POST') {
            $date        = date('Y-m-d');
            $sources     = json_encode($body['sources'] ?? []);
            $pages       = json_encode($body['pages'] ?? []);
            $newRet      = json_encode($body['newReturning'] ?? []);
            $cats        = json_encode($body['categories'] ?? []);

            $stmt = $db->prepare("INSERT INTO visitors (id,count,sources,pages,newReturning,categories) VALUES (?,1,?,?,?,?) ON DUPLICATE KEY UPDATE count=count+1, sources=?, pages=?, newReturning=?, categories=?");
            $stmt->bind_param('sssssssss', $date, $sources, $pages, $newRet, $cats, $sources, $pages, $newRet, $cats);
            $stmt->execute();
            echo json_encode(['success' => true]);
        }
        break;

    // ── PHOTOS ─────────────────────────────────────────────
    case 'photos':
        if ($method === 'GET') {
            $rows = $db->query("SELECT * FROM photos ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
        }
        break;

    // ── TESTIMONIALS ───────────────────────────────────────
    case 'testimonials':
        if ($method === 'GET') {
            $rows = $db->query("SELECT * FROM testimonials ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
            echo json_encode(['success' => true, 'data' => $rows]);
        }
        break;

    default:
        echo json_encode(['error' => 'invalid route']);
        break;
}

$db->close();
?>
