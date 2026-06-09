<?php
// ═══════════════════════════════════════════════
//  khedmotcenter.com — api.php
//  MySQL/PDO backend — Firebase replacement
// ═══════════════════════════════════════════════

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ── DB Connection ──────────────────────────────
$dsn = 'mysql:host=localhost;dbname=khedmotdb;charset=utf8mb4';
try {
    $pdo = new PDO($dsn, 'khedmotuser', 'khedmot@722', [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'DB connection failed']);
    exit;
}

// ── Helper ─────────────────────────────────────
function ok($data)  { echo json_encode(['success' => true, 'data' => $data]); exit; }
function err($msg, $code = 400) { http_response_code($code); echo json_encode(['success' => false, 'error' => $msg]); exit; }
function uid()      { return bin2hex(random_bytes(10)); }
function input()    { return json_decode(file_get_contents('php://input'), true) ?? []; }

$action = $_GET['action'] ?? '';

// ══════════════════════════════════════════════
//  POSTS
// ══════════════════════════════════════════════

// পোস্ট লিস্ট (home/center — created DESC)
if ($action === 'posts_list') {
    $limit  = min((int)($_GET['limit'] ?? 20), 500);
    $after  = (int)($_GET['after'] ?? 0);   // pagination: last created value
    $since  = (int)($_GET['since'] ?? 0);   // preview: created >= since

    if ($since > 0) {
        $st = $pdo->prepare('SELECT * FROM posts WHERE created >= ? ORDER BY created DESC LIMIT ?');
        $st->execute([$since, $limit]);
    } elseif ($after > 0) {
        $st = $pdo->prepare('SELECT * FROM posts WHERE created < ? ORDER BY created DESC LIMIT ?');
        $st->execute([$after, $limit]);
    } else {
        $st = $pdo->prepare('SELECT * FROM posts ORDER BY created DESC LIMIT ?');
        $st->execute([$limit]);
    }
    $rows = $st->fetchAll();
    foreach ($rows as &$r) {
        $r['cats'] = json_decode($r['cats'] ?? '[]', true);
        $r['hasNumber'] = (bool)$r['hasNumber'];
    }
    ok($rows);
}

// পোস্ট একটি (single)
if ($action === 'post_get') {
    $id = $_GET['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) err('not found', 404);
    $row['cats'] = json_decode($row['cats'] ?? '[]', true);
    $row['hasNumber'] = (bool)$row['hasNumber'];
    ok($row);
}

// পোস্ট যোগ (admin)
if ($action === 'post_add') {
    $d = input();
    $id = uid();
    $st = $pdo->prepare('INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber)
                         VALUES (?,?,?,?,?,?,?,?,0,0,?,?)');
    $st->execute([
        $id,
        $d['text']    ?? '',
        $d['author']  ?? 'Admin',
        json_encode($d['cats'] ?? []),
        $d['mainCat'] ?? '',
        $d['subCat']  ?? '',
        $d['created'] ?? (time() * 1000),
        $d['timeStr'] ?? '',
        $d['imgUrl']  ?? '',
        isset($d['hasNumber']) ? (int)$d['hasNumber'] : 0,
    ]);
    ok(['id' => $id]);
}

// পোস্ট আপডেট (admin edit)
if ($action === 'post_update') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('UPDATE posts SET text=?,author=?,cats=?,mainCat=?,subCat=?,timeStr=?,imgUrl=?,hasNumber=? WHERE id=?');
    $st->execute([
        $d['text']    ?? '',
        $d['author']  ?? 'Admin',
        json_encode($d['cats'] ?? []),
        $d['mainCat'] ?? '',
        $d['subCat']  ?? '',
        $d['timeStr'] ?? '',
        $d['imgUrl']  ?? '',
        isset($d['hasNumber']) ? (int)$d['hasNumber'] : 0,
        $id,
    ]);
    ok(['id' => $id]);
}

// পোস্ট ডিলিট
if ($action === 'post_delete') {
    $d  = input();
    $id = $d['id'] ?? $_GET['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('DELETE FROM posts WHERE id = ?');
    $st->execute([$id]);
    ok(['deleted' => $id]);
}

// পুরনো পোস্ট ডিলিট (60 ঘণ্টার বেশি)
if ($action === 'posts_cleanup') {
    $limit = (int)($_GET['before'] ?? 0);
    if (!$limit) err('before required');
    $st = $pdo->prepare('DELETE FROM posts WHERE created <= ?');
    $st->execute([$limit]);
    ok(['deleted' => $st->rowCount()]);
}

// লাইক আপডেট
if ($action === 'post_like') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $inc = ($d['inc'] ?? 1) > 0 ? 1 : -1;
    $st = $pdo->prepare('UPDATE posts SET likes = GREATEST(0, likes + ?) WHERE id = ?');
    $st->execute([$inc, $id]);
    $st2 = $pdo->prepare('SELECT likes FROM posts WHERE id = ?');
    $st2->execute([$id]);
    $row = $st2->fetch();
    ok(['likes' => (int)($row['likes'] ?? 0)]);
}

// ভিউ বাড়ানো
if ($action === 'post_view') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?');
    $st->execute([$id]);
    ok(['ok' => true]);
}

// সবচেয়ে বেশি দেখা পোস্ট
if ($action === 'posts_top_views') {
    $limit = min((int)($_GET['limit'] ?? 10), 50);
    $st = $pdo->prepare('SELECT * FROM posts ORDER BY views DESC LIMIT ?');
    $st->execute([$limit]);
    $rows = $st->fetchAll();
    foreach ($rows as &$r) $r['cats'] = json_decode($r['cats'] ?? '[]', true);
    ok($rows);
}

// ══════════════════════════════════════════════
//  USERS
// ══════════════════════════════════════════════

// লগইন
if ($action === 'user_login') {
    $d   = input();
    $mob = $d['mobile']   ?? '';
    $pwd = $d['password'] ?? '';
    if (!$mob || !$pwd) err('mobile ও password দিন');
    $st = $pdo->prepare('SELECT * FROM users WHERE mobile = ?');
    $st->execute([$mob]);
    $u = $st->fetch();
    if (!$u) err('এই মোবাইলে কোনো অ্যাকাউন্ট নেই');
    if ($u['password'] !== $pwd) err('পাসওয়ার্ড সঠিক নয়');
    unset($u['password']);
    ok($u);
}

// রেজিস্ট্রেশন
if ($action === 'user_add') {
    $d   = input();
    $mob = $d['mobile'] ?? '';
    if (!$mob) err('mobile required');
    // duplicate check
    $chk = $pdo->prepare('SELECT id FROM users WHERE mobile = ?');
    $chk->execute([$mob]);
    if ($chk->fetch()) err('এই মোবাইল নম্বর দিয়ে আগেই রেজিস্ট্রেশন হয়েছে');
    $id  = uid();
    $now = isset($d['createdAt']) ? (int)$d['createdAt'] : (time() * 1000);
    $exp = $now + (20 * 24 * 3600 * 1000);
    $st  = $pdo->prepare('INSERT INTO users (id,name,mobile,password,createdAt,expiresAt) VALUES (?,?,?,?,?,?)');
    $st->execute([$id, $d['name'] ?? '', $mob, $d['password'] ?? '', $now, $exp]);
    ok(['id' => $id, 'expiresAt' => $exp]);
}

// ইউজার লিস্ট (admin)
if ($action === 'users_list') {
    $limit = min((int)($_GET['limit'] ?? 200), 500);
    $st = $pdo->prepare('SELECT id,name,mobile,createdAt,expiresAt FROM users ORDER BY createdAt DESC LIMIT ?');
    $st->execute([$limit]);
    ok($st->fetchAll());
}

// একজন ইউজার
if ($action === 'user_get') {
    $id = $_GET['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('SELECT id,name,mobile,createdAt,expiresAt FROM users WHERE id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    if (!$u) err('not found', 404);
    ok($u);
}

// মেয়াদ বাড়ানো (+10 দিন)
if ($action === 'user_extend') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('SELECT expiresAt FROM users WHERE id = ?');
    $st->execute([$id]);
    $u = $st->fetch();
    if (!$u) err('not found', 404);
    $cur = max((int)$u['expiresAt'], time() * 1000);
    $new = $cur + (10 * 24 * 3600 * 1000);
    $pdo->prepare('UPDATE users SET expiresAt = ? WHERE id = ?')->execute([$new, $id]);
    ok(['expiresAt' => $new]);
}

// ইউজার আপডেট (name / password)
if ($action === 'user_update') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $fields = [];
    $vals   = [];
    if (isset($d['name']))     { $fields[] = 'name = ?';     $vals[] = $d['name']; }
    if (isset($d['password'])) { $fields[] = 'password = ?'; $vals[] = $d['password']; }
    if (!$fields) err('কিছু আপডেট করার নেই');
    $vals[] = $id;
    $pdo->prepare('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
    ok(['id' => $id]);
}

// ইউজার ডিলিট
if ($action === 'user_delete') {
    $d  = input();
    $id = $d['id'] ?? $_GET['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare('DELETE FROM users WHERE id = ?')->execute([$id]);
    ok(['deleted' => $id]);
}

// ══════════════════════════════════════════════
//  PENDING POSTS
// ══════════════════════════════════════════════

// সাবমিট (user)
if ($action === 'pending_add') {
    $d  = input();
    $id = uid();
    $st = $pdo->prepare('INSERT INTO pending_posts (id,text,mobile,cat,cats,mainCat,catLabel,timeStr,created,status)
                         VALUES (?,?,?,?,?,?,?,?,?,?)');
    $st->execute([
        $id,
        $d['text']     ?? '',
        $d['mobile']   ?? '',
        $d['cat']      ?? '',
        json_encode($d['cats'] ?? []),
        $d['mainCat']  ?? '',
        $d['catLabel'] ?? '',
        $d['timeStr']  ?? '',
        $d['created']  ?? (time() * 1000),
        'pending',
    ]);
    ok(['id' => $id]);
}

// পেন্ডিং লিস্ট (admin)
if ($action === 'pending_list') {
    $status = $_GET['status'] ?? 'pending';
    $st = $pdo->prepare('SELECT * FROM pending_posts WHERE status = ? ORDER BY created DESC LIMIT 100');
    $st->execute([$status]);
    $rows = $st->fetchAll();
    foreach ($rows as &$r) $r['cats'] = json_decode($r['cats'] ?? '[]', true);
    ok($rows);
}

// একটি পেন্ডিং পোস্ট
if ($action === 'pending_get') {
    $id = $_GET['id'] ?? '';
    if (!$id) err('id required');
    $st = $pdo->prepare('SELECT * FROM pending_posts WHERE id = ?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) err('not found', 404);
    $row['cats'] = json_decode($row['cats'] ?? '[]', true);
    ok($row);
}

// Approve — pending থেকে posts এ নিয়ে যাওয়া
if ($action === 'pending_approve') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');

    $st = $pdo->prepare('SELECT * FROM pending_posts WHERE id = ?');
    $st->execute([$id]);
    $p = $st->fetch();
    if (!$p) err('not found', 404);

    $newId = uid();
    $ins = $pdo->prepare('INSERT INTO posts (id,text,author,cats,mainCat,subCat,created,timeStr,likes,views,imgUrl,hasNumber)
                          VALUES (?,?,?,?,?,?,?,?,0,0,?,0)');
    $ins->execute([
        $newId,
        $p['text'],
        $p['mobile'],
        $p['cats'],
        $p['mainCat'],
        $p['cat'],
        $p['created'],
        $p['timeStr'],
        '',
    ]);
    $pdo->prepare("UPDATE pending_posts SET status = 'approved' WHERE id = ?")->execute([$id]);
    ok(['postId' => $newId]);
}

// Reject
if ($action === 'pending_reject') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare("UPDATE pending_posts SET status = 'rejected' WHERE id = ?")->execute([$id]);
    ok(['id' => $id]);
}

// ══════════════════════════════════════════════
//  TESTIMONIALS
// ══════════════════════════════════════════════

// যোগ করা
if ($action === 'testimonial_add') {
    $d  = input();
    $id = uid();
    $st = $pdo->prepare('INSERT INTO testimonials (id,name,area,text,cat,catLabel,userName,created,approved)
                         VALUES (?,?,?,?,?,?,?,?,0)');
    $st->execute([
        $id,
        $d['name']     ?? '',
        $d['area']     ?? '',
        $d['text']     ?? '',
        $d['cat']      ?? '',
        $d['catLabel'] ?? '',
        $d['userName'] ?? '',
        $d['created']  ?? (time() * 1000),
    ]);
    ok(['id' => $id]);
}

// লিস্ট
if ($action === 'testimonials_list') {
    $approvedOnly = isset($_GET['approved']) ? (int)$_GET['approved'] : -1;
    $limit = min((int)($_GET['limit'] ?? 100), 200);
    if ($approvedOnly >= 0) {
        $st = $pdo->prepare('SELECT * FROM testimonials WHERE approved = ? ORDER BY created DESC LIMIT ?');
        $st->execute([$approvedOnly, $limit]);
    } else {
        $st = $pdo->prepare('SELECT * FROM testimonials ORDER BY created DESC LIMIT ?');
        $st->execute([$limit]);
    }
    ok($st->fetchAll());
}

// Approve
if ($action === 'testimonial_approve') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare('UPDATE testimonials SET approved = 1 WHERE id = ?')->execute([$id]);
    ok(['id' => $id]);
}

// টেক্সট আপডেট
if ($action === 'testimonial_update') {
    $d  = input();
    $id = $d['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare('UPDATE testimonials SET text = ? WHERE id = ?')->execute([$d['text'] ?? '', $id]);
    ok(['id' => $id]);
}

// ডিলিট
if ($action === 'testimonial_delete') {
    $d  = input();
    $id = $d['id'] ?? $_GET['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare('DELETE FROM testimonials WHERE id = ?')->execute([$id]);
    ok(['deleted' => $id]);
}

// ══════════════════════════════════════════════
//  VISITORS (Analytics)
// ══════════════════════════════════════════════

// আজকের ডেটা পাওয়া
if ($action === 'visitor_get') {
    $date = $_GET['date'] ?? date('Y-m-d');
    $st = $pdo->prepare('SELECT * FROM visitors WHERE id = ?');
    $st->execute([$date]);
    $row = $st->fetch();
    if (!$row) {
        ok(['exists' => false, 'id' => $date, 'count' => 0,
            'sources' => [], 'pages' => [], 'newReturning' => [], 'categories' => []]);
    }
    $row['sources']      = json_decode($row['sources']      ?? '{}', true);
    $row['pages']        = json_decode($row['pages']        ?? '{}', true);
    $row['newReturning'] = json_decode($row['newReturning'] ?? '{}', true);
    $row['categories']   = json_decode($row['categories']   ?? '{}', true);
    $row['exists']       = true;
    ok($row);
}

// ভিজিটর আপডেট (upsert)
if ($action === 'visitor_update') {
    $d    = input();
    $date = $d['date'] ?? date('Y-m-d');
    // upsert
    $st = $pdo->prepare('SELECT id FROM visitors WHERE id = ?');
    $st->execute([$date]);
    if ($st->fetch()) {
        $fields = [];
        $vals   = [];
        if (isset($d['count'])) { $fields[] = 'count = count + ?'; $vals[] = (int)$d['count']; }
        foreach (['sources','pages','newReturning','categories'] as $col) {
            if (isset($d[$col])) { $fields[] = "$col = ?"; $vals[] = json_encode($d[$col]); }
        }
        if ($fields) {
            $vals[] = $date;
            $pdo->prepare('UPDATE visitors SET ' . implode(', ', $fields) . ' WHERE id = ?')->execute($vals);
        }
    } else {
        $ins = $pdo->prepare('INSERT INTO visitors (id,count,sources,pages,newReturning,categories)
                              VALUES (?,?,?,?,?,?)');
        $ins->execute([
            $date,
            (int)($d['count'] ?? 1),
            json_encode($d['sources']      ?? []),
            json_encode($d['pages']        ?? []),
            json_encode($d['newReturning'] ?? []),
            json_encode($d['categories']   ?? []),
        ]);
    }
    ok(['date' => $date]);
}

// ভিজিটর লিস্ট (admin analytics)
if ($action === 'visitors_list') {
    $limit = min((int)($_GET['limit'] ?? 60), 365);
    $since = $_GET['since'] ?? '';
    if ($since) {
        $st = $pdo->prepare('SELECT * FROM visitors WHERE id >= ? ORDER BY id ASC LIMIT ?');
        $st->execute([$since, $limit]);
    } else {
        $st = $pdo->prepare('SELECT * FROM visitors ORDER BY id DESC LIMIT ?');
        $st->execute([$limit]);
    }
    $rows = $st->fetchAll();
    foreach ($rows as &$r) {
        $r['sources']      = json_decode($r['sources']      ?? '{}', true);
        $r['pages']        = json_decode($r['pages']        ?? '{}', true);
        $r['newReturning'] = json_decode($r['newReturning'] ?? '{}', true);
        $r['categories']   = json_decode($r['categories']   ?? '{}', true);
    }
    ok($rows);
}

// ══════════════════════════════════════════════
//  PHOTOS
// ══════════════════════════════════════════════

// ফটো যোগ
if ($action === 'photo_add') {
    $d  = input();
    $id = uid();
    $st = $pdo->prepare('INSERT INTO photos (id,imgUrl,caption,author,timeStr,created) VALUES (?,?,?,?,?,?)');
    $st->execute([
        $id,
        $d['imgUrl']  ?? '',
        $d['caption'] ?? '',
        $d['author']  ?? 'Admin',
        $d['timeStr'] ?? '',
        $d['created'] ?? (time() * 1000),
    ]);
    ok(['id' => $id]);
}

// ফটো লিস্ট
if ($action === 'photos_list') {
    $limit = min((int)($_GET['limit'] ?? 50), 200);
    $st = $pdo->prepare('SELECT * FROM photos ORDER BY created DESC LIMIT ?');
    $st->execute([$limit]);
    ok($st->fetchAll());
}

// ফটো ডিলিট
if ($action === 'photo_delete') {
    $d  = input();
    $id = $d['id'] ?? $_GET['id'] ?? '';
    if (!$id) err('id required');
    $pdo->prepare('DELETE FROM photos WHERE id = ?')->execute([$id]);
    ok(['deleted' => $id]);
}

// ══════════════════════════════════════════════
//  PRESENCE (Active Users)
// ══════════════════════════════════════════════

if ($action === 'presence_ping') {
    // presence টেবিল না থাকলে তৈরি করা
    $pdo->exec('CREATE TABLE IF NOT EXISTS presence (
        id VARCHAR(50) PRIMARY KEY,
        expires BIGINT,
        updatedAt BIGINT
    )');
    $d   = input();
    $sid = $d['sessionId'] ?? '';
    if (!$sid) err('sessionId required');
    $exp = (time() + 90) * 1000;
    $now = time() * 1000;
    $pdo->prepare('INSERT INTO presence (id,expires,updatedAt) VALUES (?,?,?)
                   ON DUPLICATE KEY UPDATE expires=?, updatedAt=?')
        ->execute([$sid, $exp, $now, $exp, $now]);
    // পুরনো expire হওয়া session মুছে দেওয়া
    $pdo->prepare('DELETE FROM presence WHERE expires < ?')->execute([time() * 1000]);
    $cnt = $pdo->query('SELECT COUNT(*) as c FROM presence')->fetch()['c'];
    ok(['active' => (int)$cnt]);
}

if ($action === 'presence_count') {
    $pdo->exec('CREATE TABLE IF NOT EXISTS presence (
        id VARCHAR(50) PRIMARY KEY,
        expires BIGINT,
        updatedAt BIGINT
    )');
    $pdo->prepare('DELETE FROM presence WHERE expires < ?')->execute([time() * 1000]);
    $cnt = $pdo->query('SELECT COUNT(*) as c FROM presence')->fetch()['c'];
    ok(['active' => (int)$cnt]);
}

// ══════════════════════════════════════════════
//  API READY CHECK
// ══════════════════════════════════════════════

if ($action === 'ping') {
    ok(['status' => 'ok', 'time' => time()]);
}

// Unknown action
err('Unknown action: ' . $action, 404);
