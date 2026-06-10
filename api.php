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
    $email = $body['email'] ?? '';
    if (!$email) {
        echo json_encode(['success' => false]);
        exit();
    }
    $stmt = $db->prepare("SELECT id,name,email,mobile,role,expiresAt FROM users WHERE email=?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user) {
        echo json_encode(['success' => true, 'role' => $user['role'], 'data' => $user]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit();
} sed -i '66a\
\
if ($action === '"'"'admin_create_post'"'"') {\
    $id      = uniqid('"'"'p_'"'"', true);\
    $content = $body['"'"'content'"'"'] ?? '"'"''"'"';\
    $imgUrl  = $body['"'"'image_base64'"'"'] ?? '"'"''"'"';\
    $created = time() * 1000;\
    $timeStr = date('"'"'d M Y'"'"');\
    $stmt = $db->prepare("INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber) VALUES (?,?,'"'"'admin'"'"','"'"'[]'"'"','"'"''"'"','"'"''"'"',?,?,0,0,?,0)");\
    $stmt->bind_param('"'"'sssss'"'"', $id, $content, $created, $timeStr, $imgUrl);\
    $stmt->execute();\
    echo json_encode(['"'"'success'"'"' => true, '"'"'id'"'"' => $id]);\
    exit();\
}\
\
if ($action === '"'"'admin_get_posts'"'"') {\
    $rows = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);\
    foreach ($rows as &$row) {\
        $row['"'"'cats'"'"'] = json_decode($row['"'"'cats'"'"'], true);\
    }\
    echo json_encode(['"'"'success'"'"' => true, '"'"'posts'"'"' => $rows]);\
    exit();\
}' /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
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
