<?php
header('Content-Type: application/xml; charset=utf-8');
$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) {
    http_response_code(500);
    die('Database error');
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

echo '<url><loc>https://khedmotcenter.com/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>' . "\n";

$result = $db->query("SELECT slug, created FROM posts WHERE status='active' AND slug IS NOT NULL AND slug != '' ORDER BY created DESC");

while ($row = $result->fetch_assoc()) {
    $slug = htmlspecialchars($row['slug'], ENT_QUOTES, 'UTF-8');
    $lastmod = date('Y-m-d', intval($row['created']) / 1000);
    echo '<url><loc>https://khedmotcenter.com/post/' . $slug . '</loc><lastmod>' . $lastmod . '</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>' . "\n";
}

echo '</urlset>';
$db->close();
