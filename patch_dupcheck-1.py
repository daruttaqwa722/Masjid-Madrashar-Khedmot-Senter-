with open("api.php", "r", encoding="utf-8") as f:
    content = f.read()

old = '''if ($action === "check_duplicate") {
    $text = $body["content"] ?? "";
    $dup = $db->prepare("SELECT id FROM posts WHERE compact_content=? LIMIT 1");    $dup->bind_param("s", $text);
    $dup->execute();
    $dup->store_result();
    echo json_encode(["duplicate" => $dup->num_rows > 0]);
    exit();
}'''

new = '''if ($action === "check_duplicate") {
    $text = $body["content"] ?? "";
    if (!$text) { echo json_encode(["duplicate" => false]); exit(); }

    // গত ৭ দিনের পোস্ট নিয়ে আসি
    $seven_days_ago = (time() - 7 * 24 * 3600) * 1000;
    $dup = $db->prepare("SELECT compact_content FROM posts WHERE created >= ? AND compact_content IS NOT NULL LIMIT 500");
    $dup->bind_param("i", $seven_days_ago);
    $dup->execute();
    $result = $dup->get_result();

    // নতুন পোস্টের compact version বানাই
    function make_compact($t) {
        $t = mb_strtolower($t, 'UTF-8');
        $t = preg_replace('/\s+/', '', $t);
        $t = preg_replace('/[^\p{L}\p{N}]/u', '', $t);
        return $t;
    }

    $new_compact = make_compact($text);
    $new_len = mb_strlen($new_compact, 'UTF-8');
    $is_duplicate = false;

    while ($row = $result->fetch_assoc()) {
        $existing_compact = make_compact($row['compact_content']);
        $existing_len = mb_strlen($existing_compact, 'UTF-8');
        if ($existing_len === 0 || $new_len === 0) continue;

        // similar_text দিয়ে similarity চেক
        similar_text($new_compact, $existing_compact, $percent);
        if ($percent >= 95) {
            $is_duplicate = true;
            break;
        }
    }

    echo json_encode(["duplicate" => $is_duplicate]);
    exit();
}'''

if old in content:
    content = content.replace(old, new)
    print("OK: ডুপ্লিকেট চেক আপডেট হয়েছে")
else:
    print("FAIL: পুরনো কোড পাওয়া যায়নি")

with open("api.php", "w", encoding="utf-8") as f:
    f.write(content)

print("শেষ!")
