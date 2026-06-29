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


function get2amCutoff($hours) {
    $now = time();
    $today2am = mktime(2, 0, 0, date('n',$now), date('j',$now), date('Y',$now));
    if ($now < $today2am) $today2am -= 86400;
    $days = ceil($hours / 24);
    return ($today2am - $days * 86400) * 1000;
}

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

function autoSEO($text, $category, $id, $position='', $address='') {
    $catName = getCatName($category);
    $year    = date('Y');
    if ($position && $address) {
        $title = $address . ' ' . $position;
    } elseif ($position) {
        $title = $position . ' | ' . $catName;
    } elseif ($address) {
        $title = $address . ' ' . $catName;
    } else {
        $first_line = explode("\n", trim($text))[0];
        $title = mb_substr($first_line, 0, 55);
    }
    $slugWords  = preg_split('/\s+/u', trim($title), -1, PREG_SPLIT_NO_EMPTY);
    $slugWords  = array_slice($slugWords, 0, 6);
    $slugBase   = implode('-', $slugWords);
    $slugBase   = preg_replace('/[^\p{L}\p{N}\-]/u', '', $slugBase);
    $slugBase   = preg_replace('/-+/', '-', $slugBase);
    $slugBase   = trim($slugBase, '-');
    if (empty($slugBase)) $slugBase = 'post';
    $slug       = $slugBase . '-' . substr(md5($id), 0, 6);
    $meta_title = mb_substr($title, 0, 55) . ' | খেদমত সেন্টার';
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
    $mobile       = $body['mobile'] ?? '';
    $pass         = $body['password'] ?? '';
    $device_token = $body['device_token'] ?? '';
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
    if ($user['device_token'] && $device_token && $user['device_token'] !== $device_token) {
        echo json_encode(['success' => false, 'message' => 'এই একাউন্ট অন্য ডিভাইস থেকে ব্যবহার হচ্ছে। যোগাযোগ করুন।']);
        exit();
    }
    if (!$user['device_token'] && $device_token) {
        $upd = $db->prepare("UPDATE users SET device_token=? WHERE id=?");
        $upd->bind_param('ss', $device_token, $user['id']);
        $upd->execute();
    }
    unset($user['password']); $user['created_at'] = $user['createdAt'] ? date('d/m/Y', intval($user['createdAt']) / 1000) : ''; $user['expiry_date'] = $user['expiresAt'] ? date('d/m/Y', intval($user['expiresAt']) / 1000) : '';
    echo json_encode(['success' => true, 'user' => $user]);
    exit();
}

// REGISTER
if ($action === 'register') {
    $name    = $body['name'] ?? '';
    $mobile  = $body['mobile'] ?? '';
    $address = $body['address'] ?? '';
    if (!$name || !$mobile) { echo json_encode(['success' => false, 'message' => 'সব তথ্য পূরণ করুন।']); exit(); }
    $check = $db->prepare("SELECT id FROM users WHERE mobile=? LIMIT 1");
    $check->bind_param('s', $mobile);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) { echo json_encode(['success' => false, 'message' => 'এই মোবাইল নম্বর আগেই নিবন্ধিত।']); exit(); }
    $id        = uniqid('u_', true);
    $createdAt = time() * 1000;
    $expiresAt = $createdAt + (30 * 86400 * 1000);
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, address, password, plain_pass, role, createdAt, expiresAt) VALUES (?,?,?,?,?,?,'user',?,?)");
    $empty = '';
    $stmt->bind_param('ssssssii', $id, $name, $mobile, $address, $empty, $empty, $createdAt, $expiresAt);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
// VISITOR TRACKING
if ($action === 'track_visitor') {
    $vid = $body['visitor_id'] ?? '';
    $source = $body['source'] ?? 'direct';
    if (!$vid) { echo json_encode(['success'=>false]); exit(); }
    $now = time() * 1000;
    $check = $db->prepare("SELECT visitor_id FROM visitor_logs WHERE visitor_id=?");
    $check->bind_param('s', $vid);
    $check->execute();
    $isNew = $check->get_result()->num_rows === 0;
    $stmt = $db->prepare("INSERT INTO visitor_logs (visitor_id, first_visit, last_visit, visit_count, source) VALUES (?,?,?,1,?) ON DUPLICATE KEY UPDATE last_visit=?, visit_count=visit_count+1");
    $stmt->bind_param('siisi', $vid, $now, $now, $source, $now);
    $stmt->execute();
    echo json_encode(['success'=>true, 'isNew'=>$isNew]);
    exit();
}
// GET VISITOR STATS (ADMIN ONLY)
if ($action === 'get_visitor_stats') {
    $token = $body['token'] ?? '';
    if ($token !== 'admin_session') {
        echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
        exit();
    }
    $now = time();
    $todayStart = strtotime('today') * 1000;
    $yesterdayStart = strtotime('yesterday') * 1000;
    $sevenDaysStart = ($now - 7*86400) * 1000;

    function countVisitors($db, $since, $until, $isNew) {
        if ($isNew) {
            $sql = "SELECT COUNT(*) as cnt FROM visitor_logs WHERE first_visit>=? AND first_visit<?";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ii', $since, $until);
        } else {
            $sql = "SELECT COUNT(*) as cnt FROM visitor_logs WHERE last_visit>=? AND last_visit<? AND visit_count>1";
            $stmt = $db->prepare($sql);
            $stmt->bind_param('ii', $since, $until);
        }
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }

    $nowMs = $now * 1000;
    $todayNew = countVisitors($db, $todayStart, $nowMs, true);
    $todayReturning = countVisitors($db, $todayStart, $nowMs, false);
    $yestNew = countVisitors($db, $yesterdayStart, $todayStart, true);
    $yestReturning = countVisitors($db, $yesterdayStart, $todayStart, false);
    $weekNew = countVisitors($db, $sevenDaysStart, $nowMs, true);
    $weekReturning = countVisitors($db, $sevenDaysStart, $nowMs, false);

    $totalAll = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs")->fetch_assoc()['cnt'];
    $totalReturning = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE visit_count>1")->fetch_assoc()['cnt'];
    $totalNew = $totalAll - $totalReturning;

    $sourceRows = $db->query("SELECT source, COUNT(*) as cnt FROM visitor_logs GROUP BY source ORDER BY cnt DESC")->fetch_all(MYSQLI_ASSOC);
    $sources = [];
    foreach ($sourceRows as $row) { $sources[$row['source']] = (int)$row['cnt']; }
    $sourceDetail = [];
    $allSources = array_keys($sources);
    foreach ($allSources as $src) {
        $todayCnt = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE source='$src' AND first_visit>=$todayStart")->fetch_assoc()['cnt'];
        $yestCnt = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE source='$src' AND first_visit>=$yesterdayStart AND first_visit<$todayStart")->fetch_assoc()['cnt'];
        $weekCnt = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE source='$src' AND first_visit>=$sevenDaysStart")->fetch_assoc()['cnt'];
        $totalCnt = (int)$sources[$src];
        $sourceDetail[$src] = ['today'=>$todayCnt, 'yesterday'=>$yestCnt, 'week'=>$weekCnt, 'total'=>$totalCnt];
    }

    echo json_encode([
        'success'=>true,
        'today_new'=>$todayNew, 'today_returning'=>$todayReturning,
        'yesterday_new'=>$yestNew, 'yesterday_returning'=>$yestReturning,
        'week_new'=>$weekNew, 'week_returning'=>$weekReturning,
        'total'=>$totalAll, 'total_new'=>$totalNew, 'total_returning'=>$totalReturning,
        'sources'=>$sources,
        'source_detail'=>$sourceDetail
    ]);
    exit();
}
if ($action === 'get_public_news') {
    $cat    = $body['category'] ?? $_GET['category'] ?? '';
    $hours  = intval($body['hours'] ?? $_GET['hours'] ?? 0);
    $limit  = intval($body['limit'] ?? 50);
    $offset = intval($body['offset'] ?? 0);
    $where  = "WHERE status='active'";
    $params = [];
    $types  = '';
    if ($cat) { $where .= " AND (category=? OR cats LIKE ?)"; $params[] = $cat; $params[] = '%'.$cat.'%'; $types .= 'ss'; }
    if ($hours > 0) {
        // প্রতি রাত ২টায় জানালা জুলায় -- দিন হিসাবে cutoff
        $now = time();
        $today2am = mktime(2, 0, 0, date('n',$now), date('j',$now), date('Y',$now));
        if ($now < $today2am) $today2am -= 86400; // আজ রাত ২টা পার না হলে গতকালের রাত ২টা ধরী
        $days = ceil($hours / 24);
        $since = ($today2am - $days * 86400) * 1000;
        $where .= " AND created >= ?"; $params[] = $since; $types .= 'i';
    }
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
    unset($user['password']); $user['created_at'] = $user['createdAt'] ? date('d/m/Y', intval($user['createdAt']) / 1000) : ''; $user['expiry_date'] = $user['expiresAt'] ? date('d/m/Y', intval($user['expiresAt']) / 1000) : '';
    $now       = time() * 1000;
    $days_left = $user['expiresAt'] ? (int)(($user['expiresAt'] - $now) / 86400000) : 0;
    $is_active = $days_left >= 0;
    $cat    = $body['category'] ?? '';
    $hours  = intval($body['hours'] ?? 0);
    $where  = "WHERE status='active'";
    $params = [];
    $types  = '';
    if ($cat) { $where .= " AND (category=? OR cats LIKE ?)"; $params[] = $cat; $params[] = '%'.$cat.'%'; $types .= 'ss'; }
    if ($hours > 0) {
        // প্রতি রাত ২টায় জানালা জুলায় -- দিন হিসাবে cutoff
        $now = time();
        $today2am = mktime(2, 0, 0, date('n',$now), date('j',$now), date('Y',$now));
        if ($now < $today2am) $today2am -= 86400; // আজ রাত ২টা পার না হলে গতকালের রাত ২টা ধরী
        $days = ceil($hours / 24);
        $since = ($today2am - $days * 86400) * 1000;
        $where .= " AND created >= ?"; $params[] = $since; $types .= 'i';
    }
    $limit2 = intval($body['limit'] ?? 10);
    $offset2 = intval($body['offset'] ?? 0);
    $params[] = $limit2;
    $params[] = $offset2;
    $types   .= 'ii';
    $stmt2 = $db->prepare("SELECT * FROM posts $where ORDER BY created DESC LIMIT ? OFFSET ?");
    $stmt2->bind_param($types, ...$params);
    $stmt2->execute();
    $rows  = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'user' => $user, 'days_left' => $days_left, 'is_active' => $is_active, 'posts' => $posts]);
    exit();
}


// 24/48/72 ঘণ্টার ফিল্টার count
if ($action === 'get_filter_counts') {
    $hours = [24, 48, 72];
    $result = [];
    $cats = ['mosque-jobs', 'male-madrasa-jobs', 'female-madrasa-jobs'];
    foreach ($hours as $h) {
        $since = get2amCutoff($h);
        // ক্যাটাগরি অনুযায়ী কাউন্ট
        foreach ($cats as $cat) {
            $stmt2 = $db->prepare("SELECT COUNT(*) as cnt FROM posts WHERE status='active' AND created>=? AND (category=? OR cats LIKE ?)");
            $like = '%' . $cat . '%';
            $stmt2->bind_param('sss', $since, $cat, $like);
            $stmt2->execute();
            $result['cat'][$h][$cat] = (int)$stmt2->get_result()->fetch_assoc()['cnt'];
        }
        // মোট = তিন ক্যাটাগরির যোগফল
        $result[$h] = ($result['cat'][$h]['mosque-jobs'] ?? 0) + ($result['cat'][$h]['male-madrasa-jobs'] ?? 0) + ($result['cat'][$h]['female-madrasa-jobs'] ?? 0);
    }
    echo json_encode(['success' => true, 'counts' => $result]);
    exit();
}

// ৭২ ঘণ্টার পোস্ট count
if ($action === 'get_72h_counts') {
    $since = get2amCutoff(72);
    $cats = ['mosque-jobs', 'male-madrasa-jobs', 'female-madrasa-jobs'];
    $counts = [];
    foreach ($cats as $cat) {
        $like = '%' . $cat . '%';
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM posts WHERE status='active' AND (category=? OR category=REPLACE(?,'-jobs','') OR cats LIKE ?) AND created>=?");
        $stmt->bind_param('ssss', $cat, $cat, $like, $since);
        $stmt->execute();
        $counts[$cat] = (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }
    // মোট = তিন ক্যাটাগরির যোগফল
    $total = ($counts['mosque-jobs'] ?? 0) + ($counts['male-madrasa-jobs'] ?? 0) + ($counts['female-madrasa-jobs'] ?? 0);
    echo json_encode([
        'success' => true,
        'counts' => $counts,
        'total' => $total,
        'mosque' => $counts['mosque-jobs'] ?? 0,
        'male_madrasa' => $counts['male-madrasa-jobs'] ?? 0,
        'female_madrasa' => $counts['female-madrasa-jobs'] ?? 0
    ]);
    exit();
}
// ADMIN — GET POSTS
if ($action === 'admin_get_posts') {
    $limit  = intval($body['limit'] ?? 10);
    $offset = intval($body['offset'] ?? 0);
    $cat    = $body['category'] ?? '';
    $hours  = intval($body['hours'] ?? 0);
    $where  = "WHERE status='active'";
    $extra  = '';
    if ($cat) { $where .= " AND (category='$cat' OR cats LIKE '%$cat%')"; }
    if ($hours > 0) { $since = (time() - $hours * 3600) * 1000; $where .= " AND created >= $since"; }
    if ($cat || $hours > 0) { $limit = 99999; $offset = 0; }
    $total_stmt = $db->query("SELECT COUNT(*) as cnt FROM posts $where");
    $total = (int)$total_stmt->fetch_assoc()['cnt'];
    $stmt = $db->prepare("SELECT * FROM posts $where ORDER BY created DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $rows  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'posts' => $posts, 'total' => $total]);
    exit();
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
    $dup = $db->prepare("SELECT id FROM posts WHERE compact_content=? LIMIT 1");
    $dup->bind_param('s', $text);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) { echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে!']); exit(); }
    $title      = $body['title'] ?? '';
    $slug       = $body['slug'] ?? '';
    $meta_title = $body['meta_title'] ?? '';
    $meta_desc  = $body['meta_desc'] ?? '';
    $position   = $body['position'] ?? '';
    $address    = $body['address'] ?? '';
    list($autoTitle, $autoSlug, $autoMeta, $autoDesc) = autoSEO($text, $category, $id, $position, $address);
    if (!$title)      $title      = $autoTitle;
    if (!$slug)       $slug       = $autoSlug;
    if (!$meta_title) $meta_title = $autoMeta;
    if (!$meta_desc)  $meta_desc  = $autoDesc;
    $position = $body["position"] ?? ""; $address = $body["address"] ?? ""; if (!$title) $title = trim(($position ? $position : "") . ($address ? " - " . $address : "") . " নিয়োগ " . date("Y")); $stmt = $db->prepare("INSERT INTO posts (id, text, author, cats, mainCat, subCat, created, timeStr, likes, views, imgUrl, hasNumber, title, slug, category, meta_title, meta_desc, position, address) VALUES (?,?,?,?,?,?,?,?,0,0,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssisssssssss', $id, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum, $title, $slug, $category, $meta_title, $meta_desc, $position, $address);
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
    $cats = $body['cats'] ?? [];
    $cats_json = json_encode(array_values($cats), JSON_UNESCAPED_UNICODE);
    $stmt = $db->prepare("UPDATE posts SET text=?, title=?, category=?, cats=?, meta_title=?, meta_desc=? WHERE id=?");
    $stmt->bind_param('sssssss', $text, $title, $category, $cats_json, $meta_title, $meta_desc, $id);
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
// ADMIN -- GET PENDING POSTS
if ($action === 'admin_get_pending_posts') {
    $rows = $db->query("SELECT * FROM pending_posts WHERE status='pending' ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    foreach ($rows as &$row) {
        $row['cats'] = json_decode($row['cats'], true);
    }
    echo json_encode(['success' => true, 'posts' => $rows]);
    exit();
}
// ADMIN -- APPROVE PENDING POST (with edit)
if ($action === 'admin_approve_post') {
    $pid      = $body['id'] ?? '';
    $text     = $body['content'] ?? '';
    $category = $body['category'] ?? 'mosque';
    $title    = $body['title'] ?? '';
    $position = $body['position'] ?? '';
    $address  = $body['address'] ?? '';
    if (!$pid || !$text) { echo json_encode(['success' => false, 'message' => 'id ও content প্রয়োজন']); exit(); }

    $get = $db->prepare("SELECT * FROM pending_posts WHERE id=? LIMIT 1");
    $get->bind_param('s', $pid);
    $get->execute();
    $pending = $get->get_result()->fetch_assoc();
    if (!$pending) { echo json_encode(['success' => false, 'message' => 'পোস্ট খুঁজে পাওয়া যায়নি']); exit(); }

    $newId   = uniqid('p_', true);
    $author  = 'Admin';
    $catsArr = $body['cats'] ?? [$category];
    if (!is_array($catsArr) || !count($catsArr)) $catsArr = [$category];
    $cats = json_encode(array_values($catsArr), JSON_UNESCAPED_UNICODE);
    if (!is_array($catsArr) || !count($catsArr)) $catsArr = [$category];
    $cats = json_encode(array_values($catsArr), JSON_UNESCAPED_UNICODE);
    $mainCat = $category;
    $subCat  = '';
    $created = time() * 1000;
    $timeStr = date('d/m/Y');
    $imgUrl  = '';
    $hasNum  = 1;

    list($autoTitle, $autoSlug, $autoMeta, $autoDesc) = autoSEO($text, $category, $newId, $position, $address);
    if ($title) $autoTitle = $title;

    $stmt = $db->prepare("INSERT INTO posts (id, text, author, cats, mainCat, subCat, created, timeStr, likes, views, imgUrl, hasNumber, title, slug, category, meta_title, meta_desc, position, address) VALUES (?,?,?,?,?,?,?,?,0,0,?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssssisssssssss', $newId, $text, $author, $cats, $mainCat, $subCat, $created, $timeStr, $imgUrl, $hasNum, $autoTitle, $autoSlug, $category, $autoMeta, $autoDesc, $position, $address);
    $stmt->execute();

    $del = $db->prepare("DELETE FROM pending_posts WHERE id=?");
    $del->bind_param('s', $pid);
    $del->execute();

    echo json_encode(['success' => true, 'id' => $newId]);
    exit();
}
// ADMIN -- REJECT PENDING POST
if ($action === 'admin_reject_post') {
    $pid = $body['id'] ?? '';
    if (!$pid) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("DELETE FROM pending_posts WHERE id=?");
    $stmt->bind_param('s', $pid);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}
if ($action === 'admin_get_users') {
    $search = $body['search'] ?? '';
    if ($search) {
        $s = "%$search%";
        $stmt = $db->prepare("SELECT id, name, mobile, address, role, plain_pass, createdAt, expiresAt FROM users WHERE mobile LIKE ? OR name LIKE ? ORDER BY createdAt DESC");
        $stmt->bind_param('ss', $s, $s);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $rows = $db->query("SELECT id, name, mobile, address, role, plain_pass, createdAt, expiresAt FROM users ORDER BY createdAt DESC")->fetch_all(MYSQLI_ASSOC);
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
    $stmt = $db->prepare("INSERT INTO users (id, name, mobile, address, password, plain_pass, role, createdAt, expiresAt) VALUES (?,?,?,?,?,?,'user',?,?)");
    $stmt->bind_param('ssssssii', $id, $name, $mobile, $address, $hash, $pass, $createdAt, $expiresAt);
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
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, address=?, password=?, plain_pass=?, expiresAt=?, device_token=NULL WHERE id=?");
        $stmt->bind_param('sssssis', $name, $mobile, $address, $hash, $pass, $expiresAt, $id);
    } else {
        $stmt = $db->prepare("UPDATE users SET name=?, mobile=?, address=?, expiresAt=? WHERE id=?");
        $stmt->bind_param('sssis', $name, $mobile, $address, $expiresAt, $id);
    }
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}


// ADMIN — GET USER PASSWORD
if ($action === 'admin_get_password') {
    $token = $body['token'] ?? '';
    if ($token !== 'admin_session') { echo json_encode(['success' => false, 'message' => 'অনুমতি নেই']); exit(); }
    $id = $body['id'] ?? '';
    if (!$id) { echo json_encode(['success' => false, 'message' => 'id প্রয়োজন']); exit(); }
    $stmt = $db->prepare("SELECT plain_pass FROM users WHERE id=? LIMIT 1");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    echo json_encode(['success' => true, 'plain_pass' => $row['plain_pass'] ?? '']);
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
    $id       = uniqid('pnd_', true);
    $text     = $body['notice'] ?? $body['content'] ?? '';
    $mobile   = $body['mobile'] ?? '';
    $catsArr  = $body['categories'] ?? [];
    $cats     = json_encode($catsArr);
    $mainCat  = $catsArr[0] ?? '';
    $timeStr  = date('d/m/Y');
    $created  = time() * 1000;
    if (!$text) { echo json_encode(['success' => false, 'message' => 'পোস্ট লিখুন।']); exit(); }
    $stmt = $db->prepare("INSERT INTO pending_posts (id, text, mobile, cats, mainCat, timeStr, created, status) VALUES (?,?,?,?,?,?,?,'pending')");
    $stmt->bind_param('ssssssi', $id, $text, $mobile, $cats, $mainCat, $timeStr, $created);
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

if ($action === 'get_hourly_stats') {
    $token = $body['token'] ?? '';
    if ($token !== 'admin_session') { echo json_encode(['success'=>false]); exit(); }
    date_default_timezone_set('Asia/Dhaka');
    $bn = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $toBnNum = function($n) use ($bn) { $s=''; foreach(str_split((string)$n) as $c) $s.=is_numeric($c)?$bn[$c]:$c; return $s; };
    $bdPeriod = function($h) { if($h>=0&&$h<4) return 'রাত'; elseif($h<6) return 'ভোর'; elseif($h<12) return 'সকাল'; elseif($h==12) return 'দুপুর'; elseif($h<17) return 'বিকেল'; elseif($h<20) return 'সন্ধ্যা'; else return 'রাত'; };
    $result = [];
    for ($h = 1; $h <= 24; $h++) {
        $from = (time() - $h * 3600) * 1000;
        $to   = (time() - ($h-1) * 3600) * 1000;
        $fromTs = time() - $h * 3600;
        $toTs   = time() - ($h-1) * 3600;
        $fromHr = (int)date('G', $fromTs);
        $toHr   = (int)date('G', $toTs);
        $fromLabel = $bdPeriod($fromHr).' '.$toBnNum(date('g', $fromTs)).':'.$toBnNum(date('i', $fromTs));
        $toLabel   = $bdPeriod($toHr).' '.$toBnNum(date('g', $toTs)).':'.$toBnNum(date('i', $toTs));
        $stmt = $db->prepare("SELECT COUNT(*) as cnt FROM posts WHERE created >= ? AND created < ?");
        $stmt->bind_param('ii', $from, $to);
        $stmt->execute();
        $cnt = (int)$stmt->get_result()->fetch_assoc()['cnt'];
        $result[] = ['hour' => $h, 'from' => $fromLabel, 'to' => $toLabel, 'count' => $cnt];
    }
    echo json_encode(['success' => true, 'stats' => $result]);
    exit();
}

// CHECK DUPLICATE
if ($action === "check_duplicate") {
    $text = trim($body["content"] ?? "");
    if (!$text) { echo json_encode(["duplicate" => false]); exit(); }
    $seven_sec = time() - (7 * 24 * 3600);
    $seven_ms = $seven_sec * 1000;
    $res = $db->query("SELECT text FROM posts WHERE created >= " . intval($seven_ms) . " AND text IS NOT NULL LIMIT 1000");
    $is_dup = false;
    $new_t = preg_replace("/\s+/u", "", mb_strtolower($text, "UTF-8"));
    while ($row = $res->fetch_assoc()) {
        $ex_t = preg_replace("/\s+/u", "", mb_strtolower($row["text"], "UTF-8"));
        if (!$ex_t) continue;
        if (strlen($new_t) > 0 && strlen($ex_t) > 0) {
            $max_len = max(strlen($new_t), strlen($ex_t));
            similar_text($new_t, $ex_t, $pct);
            if ($pct >= 95) { $is_dup = true; break; }
        }
    }
    echo json_encode(["duplicate" => $is_dup]);
    exit();
}
$db->close();

// LIVE VISITOR PING
if ($action === 'live_ping') {
    $vid  = $body['visitor_id'] ?? '';
    $page = $body['page'] ?? 'home';
    if (!$vid) { echo json_encode(['success'=>false]); exit(); }
    $now = time() * 1000;
    $stmt = $db->prepare("INSERT INTO live_visitors (visitor_id, page, last_ping) VALUES (?,?,?) ON DUPLICATE KEY UPDATE page=?, last_ping=?");
    $stmt->bind_param('ssisi', $vid, $page, $now, $page, $now);
    $stmt->execute();
    echo json_encode(['success'=>true]);
    exit();
}

// LIVE VISITOR STATS
if ($action === 'get_live_visitors') {
    $token = $body['token'] ?? '';
    if ($token !== 'admin_session') { echo json_encode(['success'=>false]); exit(); }
    $cutoff = (time() - 60) * 1000;
    $db->query("DELETE FROM live_visitors WHERE last_ping < $cutoff");
    $rows = $db->query("SELECT page, COUNT(*) as cnt FROM live_visitors GROUP BY page")->fetch_all(MYSQLI_ASSOC);
    $pages = [];
    $total = 0;
    foreach ($rows as $r) { $pages[$r['page']] = (int)$r['cnt']; $total += (int)$r['cnt']; }
    echo json_encode(['success'=>true, 'pages'=>$pages, 'total'=>$total]);
    exit();
}

$db->close();
?>

