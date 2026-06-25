<?php
$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) {
    http_response_code(500);
    die('Database error');
}

$slug = $_GET['slug'] ?? '';
if (!$slug) {
    http_response_code(404);
    die('Post not found');
}

$stmt = $db->prepare("SELECT * FROM posts WHERE slug=? AND status='active' LIMIT 1");
$stmt->bind_param('s', $slug);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    http_response_code(404);
    echo '<!DOCTYPE html><html lang="bn"><head><meta charset="UTF-8"><title>পোস্ট পাওয়া যায়নি | খেদমত সেন্টার</title></head><body><h1>এই পোস্টটি খুঁজে পাওয়া যায়নি</h1><a href="/">হোমপেজে যান</a></body></html>';
    exit();
}

function escHtml($s) {
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}

function maskPhoneSimple($text) {
    $text = preg_replace_callback(
        '/(\+?880|0)(1[3-9])(\d{2})([\s\-]?)(\d{3})([\s\-]?)(\d{3})/u',
        function($m) { return $m[1].$m[2].$m[3].$m[4].$m[5].$m[6].'***'; },
        $text
    );
    $bengali = ['০','১','২','৩','৪','৫','৬','৭','৮','৯'];
    $english = ['0','1','2','3','4','5','6','7','8','9'];
    $text_en = str_replace($bengali, $english, $text);
    if ($text_en !== $text) {
        $masked_en = maskPhoneSimple($text_en);
        $text = str_replace($english, $bengali, $masked_en);
    }
    return $text;
}

$catMap = [
    'mosque-jobs' => 'মসজিদের নিয়োগ',
    'male-madrasa-jobs' => 'পুরুষ মাদ্রাসার নিয়োগ',
    'female-madrasa-jobs' => 'মহিলা মাদ্রাসার নিয়োগ',
    'mosque' => 'মসজিদের নিয়োগ',
    'male-madrasa' => 'পুরুষ মাদ্রাসার নিয়োগ',
    'female-madrasa' => 'মহিলা মাদ্রাসার নিয়োগ',
];

$title = $post['title'] ?: ($post['position'] ?: 'নিয়োগ বিজ্ঞপ্তি') . ($post['address'] ? ' - ' . $post['address'] : '');
$metaTitle = $post['meta_title'] ?: $title . ' | খেদমত সেন্টার';
$metaDesc = $post['meta_desc'] ?: mb_substr(strip_tags($post['text']), 0, 160);
$catLabel = $catMap[$post['category']] ?? '';
$dateStr = date('d F Y', intval($post['created']) / 1000);
$maskedText = maskPhoneSimple($post['text']);
$canonicalUrl = "https://khedmotcenter.com/post/" . rawurlencode($post['slug']);
?>
<!DOCTYPE html>
<html lang="bn">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= escHtml($metaTitle) ?></title>
<meta name="description" content="<?= escHtml($metaDesc) ?>">
<link rel="canonical" href="<?= escHtml($canonicalUrl) ?>">
<meta property="og:title" content="<?= escHtml($metaTitle) ?>">
<meta property="og:description" content="<?= escHtml($metaDesc) ?>">
<meta property="og:type" content="article">
<meta property="og:url" content="<?= escHtml($canonicalUrl) ?>">
<?php if (!empty($post['image_path'])): ?>
<meta property="og:image" content="<?= escHtml($post['image_path']) ?>">
<?php endif; ?>
<link rel="icon" type="image/png" href="/favicon-v2.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Hind+Siliguri:wght@400;500;600;700&family=Noto+Serif+Bengali:wght@400;700&display=swap" rel="stylesheet">
<style>
  *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Hind Siliguri', sans-serif; background: #F0F2FA; color: #12153A; line-height: 1.7; }
  .wrap { max-width: 640px; margin: 0 auto; padding: 1.2rem; }
  .top-link { display: inline-block; margin-bottom: 1rem; color: #05057D; font-weight: 700; text-decoration: none; font-size: 0.9rem; }
  .post-card { background: #fff; border-radius: 18px; box-shadow: 0 8px 32px rgba(5,5,125,0.10); overflow: hidden; }
  .post-date { background: rgba(5,5,125,0.06); padding: 0.6rem 1rem; font-size: 0.8rem; color: #4A5280; font-weight: 600; }
  .post-cat { display: inline-block; background: #e6f4ea; color: #1a7a3c; font-size: 0.78rem; padding: 3px 12px; border-radius: 20px; font-weight: 600; margin: 0.8rem 1rem 0; }
  .post-body { padding: 1rem; }
  .post-title { font-family: 'Noto Serif Bengali', serif; font-size: 1.25rem; font-weight: 700; color: #05057D; margin-bottom: 0.6rem; line-height: 1.5; }
  .post-position { font-size: 0.9rem; color: #1a7a3c; font-weight: 600; margin-bottom: 4px; }
  .post-address { font-size: 0.85rem; color: #888; margin-bottom: 10px; }
  .post-text { font-size: 0.95rem; white-space: pre-wrap; color: #12153A; }
  .post-img { width: 100%; border-radius: 12px; margin-top: 1rem; }
  .mask-info { background: rgba(5,5,125,0.06); border: 1px solid rgba(5,5,125,0.12); border-radius: 10px; padding: 0.7rem 0.9rem; margin-top: 0.9rem; font-size: 0.82rem; color: #05057D; font-weight: 600; text-align: center; }
  footer { text-align: center; padding: 2rem 1rem; font-size: 0.8rem; color: #8890B0; }
</style>
</head>
<body>
  <div class="wrap">
    <a class="top-link" href="/">← খেদমত সেন্টার হোমপেজে যান</a>
    <div class="post-card">
      <div class="post-date"><?= escHtml($dateStr) ?></div>
      <?php if ($catLabel): ?>
      <div class="post-cat"><?= escHtml($catLabel) ?></div>
      <?php endif; ?>
      <div class="post-body">
        <?php if ($post['position']): ?><div class="post-position"><?= escHtml($post['position']) ?></div><?php endif; ?>
        <?php if ($post['address']): ?><div class="post-address">📍 <?= escHtml($post['address']) ?></div><?php endif; ?>
        <?php if ($post['title']): ?><h1 class="post-title"><?= escHtml($post['title']) ?></h1><?php endif; ?>
        <div class="post-text"><?= nl2br(escHtml($maskedText)) ?></div>
        <div class="mask-info">🔒 সম্পূর্ণ নাম্বারসহ দেখতে পাসওয়ার্ড সংগ্রহ করুন</div>
        <?php if ($post['image_path']): ?><img class="post-img" src="<?= escHtml($post['image_path']) ?>" alt="<?= escHtml($title) ?>"><?php endif; ?>
      </div>
    </div>
  </div>
  <footer>© খেদমত সেন্টার · মসজিদ মাদ্রাসার খেদমতের নিয়োগ বিজ্ঞপ্তি</footer>
</body>
</html>
