<?php
$host = 'localhost';
$dbname = 'khedmotdb';
$user = 'root';
$pass = readline("MySQL password: ");
$db = new mysqli($host, $user, $pass, $dbname);
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error . "\n");
}
function banglaToSlug($text, $id) {
    $text = trim($text);
    $text = preg_replace('/[\r\n]+/', ' ', $text);
    $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    $words = array_slice($words, 0, 6);
    $slugBase = implode('-', $words);
    $slugBase = preg_replace('/[^\p{L}\p{N}\-]/u', '', $slugBase);
    $slugBase = preg_replace('/-+/', '-', $slugBase);
    $slugBase = trim($slugBase, '-');
    if (empty($slugBase)) $slugBase = 'post';
    $shortId = substr(md5($id), 0, 6);
    return $slugBase . '-' . $shortId;
}
$result = $db->query("SELECT id, text FROM posts WHERE (slug IS NULL OR slug = '') AND status='active'");
$count = 0;
$updateStmt = $db->prepare("UPDATE posts SET slug=? WHERE id=?");
while ($row = $result->fetch_assoc()) {
    $slug = banglaToSlug($row['text'] ?: 'খেদমত-পোস্ট', $row['id']);
    $updateStmt->bind_param('ss', $slug, $row['id']);
    $updateStmt->execute();
    $count++;
    echo "Updated {$row['id']} -> $slug\n";
}
echo "\nTotal updated: $count\n";
$db->close();
