f = '/home/khedmotcenter/htdocs/khedmotcenter.com/api.php'
c = open(f, 'r', encoding='utf-8').read()

old = '''    $dup = $db->prepare("SELECT id FROM posts WHERE text=? LIMIT 1");    $dup->bind_param('s', $text);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) { echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে!']); exit(); }'''

new = '''    // Duplicate check — ৯৫% similarity
    $textNorm = preg_replace('/\s+/', ' ', trim($text));
    $recent = $db->query("SELECT text FROM posts ORDER BY created DESC LIMIT 500");
    while ($row = $recent->fetch_assoc()) {
        $existNorm = preg_replace('/\s+/', ' ', trim($row['text']));
        similar_text($textNorm, $existNorm, $percent);
        if ($percent >= 95) {
            echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে! (' . round($percent) . '% মিল)']);
            exit();
        }
    }'''

if old in c:
    open(f, 'w', encoding='utf-8').write(c.replace(old, new, 1))
    print('done')
else:
    print('not found')
