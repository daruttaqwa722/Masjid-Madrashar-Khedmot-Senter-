f = '/home/khedmotcenter/htdocs/khedmotcenter.com/api.php'
c = open(f, 'r', encoding='utf-8').read()

old = '''    function countVisitors($db, $since, $until, $isNew) {
        if ($isNew) {
            $sql = "SELECT COUNT(*) as cnt FROM visitor_logs WHERE first_visit>=? AND first_visit<?";
        } else {
            $sql = "SELECT COUNT(*) as cnt FROM visitor_logs WHERE last_visit>=? AND last_visit<? AND first_visit<?";
        }
        $stmt = $db->prepare($sql);
        if ($isNew) { $stmt->bind_param('ii', $since, $until); }
        else { $stmt->bind_param('iii', $since, $until, $since); }
        $stmt->execute();
        return (int)$stmt->get_result()->fetch_assoc()['cnt'];
    }'''

new = '''    function countVisitors($db, $since, $until, $isNew) {
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
    }'''

if old in c:
    open(f, 'w', encoding='utf-8').write(c.replace(old, new, 1))
    print('done')
else:
    print('not found')
