with open("api.php", "r", encoding="utf-8") as f:
    content = f.read()

old = '''    $seven_days_ago = (time() - 7 * 24 * 3600) * 1000;
    $dup = $db->prepare("SELECT compact_content FROM posts WHERE created >= ? AND compact_content IS NOT NULL LIMIT 500");
    $dup->bind_param("i", $seven_days_ago);
    $dup->execute();
    $result = $dup->get_result();

    function make_compact2($t) {
        $t = mb_strtolower($t, "UTF-8");
        $t = preg_replace("/\\s+/", "", $t);
        $t = preg_replace("/[^\\p{L}\\p{N}]/u", "", $t);
        return $t;
    }

    $new_compact = make_compact2($text);
    $is_duplicate = false;

    while ($row = $result->fetch_assoc()) {
        $existing_compact = make_compact2($row["compact_content"]);
        if (mb_strlen($existing_compact, "UTF-8") === 0) continue;
        similar_text($new_compact, $existing_compact, $percent);
        if ($percent >= 95) { $is_duplicate = true; break; }
    }'''

new = '''    $seven_days_ago = (time() - 7 * 24 * 3600) * 1000;
    $dup = $db->prepare("SELECT text FROM posts WHERE created >= ? AND text IS NOT NULL LIMIT 500");
    $dup->bind_param("i", $seven_days_ago);
    $dup->execute();
    $result = $dup->get_result();

    function make_compact2($t) {
        $t = mb_strtolower($t, "UTF-8");
        $t = preg_replace("/\\s+/", "", $t);
        $t = preg_replace("/[^\\p{L}\\p{N}]/u", "", $t);
        return $t;
    }

    $new_compact = make_compact2($text);
    $is_duplicate = false;

    while ($row = $result->fetch_assoc()) {
        $existing_compact = make_compact2($row["text"]);
        if (mb_strlen($existing_compact, "UTF-8") === 0) continue;
        similar_text($new_compact, $existing_compact, $percent);
        if ($percent >= 95) { $is_duplicate = true; break; }
    }'''

if old in content:
    content = content.replace(old, new)
    print("OK")
else:
    print("FAIL")

with open("api.php", "w", encoding="utf-8") as f:
    f.write(content)
