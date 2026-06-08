<?php
// =============================================
// মসজিদ-মাদ্রাসার খেদমত সেন্টার — API
// =============================================

// ---------- CORS & Headers ----------
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Admin-Token');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

// ---------- DB Config ----------
define('DB_HOST', 'localhost');
define('DB_NAME', 'khedmotdb');
define('DB_USER', 'khedmotuser');
define('DB_PASS', 'khedmot@722');
define('ADMIN_TOKEN', 'khedmot_admin_jihadul_722'); // Admin requests must send this header

// ---------- Connect ----------
function db(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $pdo = new PDO(
            'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    } catch (PDOException $e) {
        resp(500, ['error' => 'DB connection failed: ' . $e->getMessage()]);
    }
    return $pdo;
}

function resp(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function isAdmin(): bool {
    $token = $_SERVER['HTTP_X_ADMIN_TOKEN'] ?? '';
    return $token === ADMIN_TOKEN;
}

function banglaTime(): string {
    date_default_timezone_set('Asia/Dhaka');
    $months = ['জানুয়ারি','ফেব্রুয়ারি','মার্চ','এপ্রিল','মে','জুন',
               'জুলাই','আগস্ট','সেপ্টেম্বর','অক্টোবর','নভেম্বর','ডিসেম্বর'];
    $d = date('j'); $m = (int)date('n') - 1; $y = date('Y');
    $t = date('g:i'); $ap = date('A') === 'AM' ? 'পূর্বাহ্ণ' : 'অপরাহ্ণ';
    return "$d {$months[$m]} $y, $t $ap";
}

// ---------- Router ----------
$method = $_SERVER['REQUEST_METHOD'];
$path   = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$parts  = explode('/', $path);

// Expect: api.php/resource[/id]  OR  ?action=resource[&id=x]
// Support both path-based and query-based routing
$resource = $parts[1] ?? ($_GET['r'] ?? '');
$id       = $parts[2] ?? ($_GET['id'] ?? null);

switch ($resource) {

    // ==============================
    // POSTS
    // ==============================
    case 'posts':
        if ($method === 'GET') {
            $limit  = min((int)($_GET['limit'] ?? 100), 200);
            $offset = (int)($_GET['offset'] ?? 0);
            $stmt = db()->prepare('SELECT * FROM posts ORDER BY created DESC LIMIT ? OFFSET ?');
            $stmt->execute([$limit, $offset]);
            $rows = $stmt->fetchAll();
            $total = db()->query('SELECT COUNT(*) FROM posts')->fetchColumn();
            resp(200, ['posts' => $rows, 'total' => (int)$total]);
        }
        if ($method === 'POST') {
            if (!isAdmin()) resp(403, ['error' => 'Unauthorized']);
            $body = json_decode(file_get_contents('php://input'), true);
            $text = trim($body['text'] ?? '');
            if (!$text) resp(400, ['error' => 'text required']);
            if (mb_strlen($text) > 2000) resp(400, ['error' => 'too long']);
            $now = (int)(microtime(true) * 1000);
            $stmt = db()->prepare('INSERT INTO posts (text, author, time_str, created) VALUES (?, ?, ?, ?)');
            $stmt->execute([$text, $body['author'] ?? 'Administrator', banglaTime(), $now]);
            $newId = db()->lastInsertId();
            resp(201, ['id' => (int)$newId, 'message' => 'পোস্ট প্রকাশিত হয়েছে']);
        }
        if ($method === 'DELETE') {
            if (!isAdmin()) resp(403, ['error' => 'Unauthorized']);
            if (!$id) resp(400, ['error' => 'id required']);
            $stmt = db()->prepare('DELETE FROM posts WHERE id = ?');
            $stmt->execute([(int)$id]);
            resp(200, ['message' => 'ডিলিট হয়েছে']);
        }
        break;

    // ==============================
    // PHOTOS
    // ==============================
    case 'photos':
        if ($method === 'GET') {
            $stmt = db()->query('SELECT * FROM photos ORDER BY created DESC');
            resp(200, ['photos' => $stmt->fetchAll()]);
        }
        if ($method === 'POST') {
            if (!isAdmin()) resp(403, ['error' => 'Unauthorized']);
            $body = json_decode(file_get_contents('php://input'), true);
            $url  = trim($body['img_url'] ?? '');
            if (!$url) resp(400, ['error' => 'img_url required']);
            $now = (int)(microtime(true) * 1000);
            $stmt = db()->prepare('INSERT INTO photos (img_url, caption, author, time_str, created) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$url, $body['caption'] ?? '', $body['author'] ?? 'Administrator', banglaTime(), $now]);
            resp(201, ['id' => (int)db()->lastInsertId(), 'message' => 'ফটো যোগ হয়েছে']);
        }
        if ($method === 'DELETE') {
            if (!isAdmin()) resp(403, ['error' => 'Unauthorized']);
            if (!$id) resp(400, ['error' => 'id required']);
            db()->prepare('DELETE FROM photos WHERE id = ?')->execute([(int)$id]);
            resp(200, ['message' => 'ফটো ডিলিট হয়েছে']);
        }
        break;

    // ==============================
    // VISITORS
    // ==============================
    case 'visitors':
        if ($method === 'GET') {
            if (!isAdmin()) resp(403, ['error' => 'Unauthorized']);
            date_default_timezone_set('Asia/Dhaka');
            $today = date('Y-m-d');
            $yest  = date('Y-m-d', strtotime('-1 day'));
            $mon   = date('Y-m');

            $rows  = db()->query('SELECT visit_date, count FROM visitors ORDER BY visit_date DESC LIMIT 90')->fetchAll();
            $total = 0; $todayC = 0; $yesterdayC = 0; $monthC = 0;
            foreach ($rows as $r) {
                $total += $r['count'];
                if ($r['visit_date'] === $today)  $todayC = $r['count'];
                if ($r['visit_date'] === $yest)   $yesterdayC = $r['count'];
                if (str_starts_with($r['visit_date'], $mon)) $monthC += $r['count'];
            }
            resp(200, [
                'today'     => $todayC,
                'yesterday' => $yesterdayC,
                'month'     => $monthC,
                'total'     => $total,
                'log'       => $rows
            ]);
        }
        if ($method === 'POST') {
            // Track a visit (called by frontend, no auth needed)
            date_default_timezone_set('Asia/Dhaka');
            $today = date('Y-m-d');
            $stmt = db()->prepare(
                'INSERT INTO visitors (visit_date, count) VALUES (?, 1)
                 ON DUPLICATE KEY UPDATE count = count + 1'
            );
            $stmt->execute([$today]);
            resp(200, ['tracked' => true]);
        }
        break;

    default:
        resp(404, ['error' => 'Unknown endpoint. Use /posts, /photos, /visitors']);
}
