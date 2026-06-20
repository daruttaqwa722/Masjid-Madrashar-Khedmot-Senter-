<?php
$db = new mysqli('localhost', 'khedmotuser', 'khedmot@722', 'khedmotdb');
$db->set_charset('utf8mb4');
if ($db->connect_error) { die("Connection failed: " . $db->connect_error . "\n"); }
echo "Connected\n";

function makeSlug($title, $id) {
    $title = trim($title);
    $words = preg_split('/\s+/u', $title, -1, PREG_SPLIT_NO_EMPTY);
    $words = array_slice($words, 0, 6);
    $slugBase = implode('-', $words);
    $slugBase = preg_replace('/[^\p{L}\p{M}\p{N}\-]/u', '', $slugBase);
    $slugBase = preg_replace('/-+/', '-', $slugBase);
    $slugBase = trim($slugBase, '-');
    if (empty($slugBase)) $slugBase = 'post';
    return $slugBase . '-' . substr(md5($id), 0, 6);
}

$result = $db->query("SELECT id, title FROM posts WHERE status='active'");
$count = 0;
$stmt = $db->prepare("UPDATE posts SET slug=? WHERE id=?");
while ($row = $result->fetch_assoc()) {
    $title = $row['title'] ?: 'খেদমত পোস্ট';
    $slug = makeSlug($title, $row['id']);
    $stmt->bind_param('ss', $slug, $row['id']);
    $stmt->execute();
    $count++;
}
echo "Updated: $count\n";
$db->close();
