if old4 in content: content = content.replace(old4, new4); print("Step 4 done")
else: print("Step 4 NOT FOUND")
old5 = "const res = await api('get_public_news', {limit: 50, offset: window._newsOffset || 20});"
new5 = "const res = await api('get_public_news', {limit: 20, offset: window._newsOffset || 10});"
if old5 in content: content = content.replace(old5, new5); print("Step 5 done")
else: print("Step 5 NOT FOUND")
old6 = "if (res.posts.length < 50) {"
new6 = "if (res.posts.length < 20) {"
if old6 in content: content = content.replace(old6, new6); print("Step 6 done")
else: print("Step 6 NOT FOUND")
open('index.html', 'w').write(content)
print("All saved")
PYEOF

oot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# python3 << 'PYEOF'
content = open('index.html', 'r').read()
old1 = "const res = await api('get_public_news', {limit: 20, offset:
0});"
new1 = "const res = await api('get_public_news', {limit: 10, offset:
0});"
if old1 in content: content = content.replace(old1, new1); print("Step 1 done")
else: print("Step 1 NOT FOUND")
old2 = "window._newsOffset = 20;"
new2 = "window._newsOffset = 10;"
if old2 in content: content = content.replace(old2, new2); print("Step 2 done")
else: print("Step 2 NOT FOUND")
old3 = "if (res.posts.length >= 20) {"
new3 = "if (res.posts.length >= 10) {"
if old3 in content: content = content.replace(old3, new3); print("StePYEOF("All saved") 'w').write(content)replace(old6, new6); print("SteStep 1 done
Step 2 done
Step 3 done
Step 4 done
Step 5 done
Step 6 done
All saved
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
PYEOF

grep -n "limit: 20" index.html
grep -n "আরো দেখুন\|posts.length < 50" index.html
sed -n '1,20p' index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com
cd /home/khedmotcenter/htdocs/khedmotcenter.com && cat > /tmp/seo.py << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

old = '<link rel="icon" type="image/png" href="favicon.png">'

new = '''<link rel="icon" type="image/png" href="favicon.png">
<meta property="og:site_name" content="খেদমত সেন্টার">
<meta name="application-name" content="খেদমত সেন্টার">
<meta name="apple-mobile-web-app-title" content="খেদমত সেন্টার">
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "খেদমত সেন্টার",
  "alternateName": "Khedmot Center",
  "url": "https://khedmotcenter.com/",
  "logo": "https://khedmotcenter.com/logo.png"
}
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "WebSite",
  "name": "খেদমত সেন্টার",
  "url": "https://khedmotcenter.com/"
}
</script>'''

if old in c:
    c = c.replace(old, new)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

python3 /tmp/seo.py
grep -n "favicon" index.html
cat > /tmp/seo2.py << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

old = '<link rel="icon" type="image/png" href="favicon-v2.png">'

new = '''<link rel="icon" type="image/png" href="favicon-v2.png">
<meta property="og:site_name" content="\u0996\u09c7\u09a6\u09ae\u09a4 \u09b8\u09c7\u09a8\u09cd\u099f\u09be\u09b0">
<meta name="application-name" content="\u0996\u09c7\u09a6\u09ae\u09a4 \u09b8\u09c7\u09a8\u09cd\u099f\u09be\u09b0">
<meta name="apple-mobile-web-app-title" content="\u0996\u09c7\u09a6\u09ae\u09a4 \u09b8\u09c7\u09a8\u09cd\u099f\u09be\u09b0">
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"Organization","name":"\u0996\u09c7\u09a6\u09ae\u09a4 \u09b8\u09c7\u09a8\u09cd\u099f\u09be\u09b0","alternateName":"Khedmot Center","url":"https://khedmotcenter.com/","logo":"https://khedmotcenter.com/logo.png"}
</script>
<script type="application/ld+json">
{"@context":"https://schema.org","@type":"WebSite","name":"\u0996\u09c7\u09a6\u09ae\u09a4 \u09b8\u09c7\u09a8\u09cd\u099f\u09be\u09b0","url":"https://khedmotcenter.com/"}
</script>'''

if old in c:
    c = c.replace(old, new)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

python3 /tmp/seo2.py
sed -n '1,20p' index.html
git add index.html && git commit -m "Add Google Site Name SEO for খেদমত সেন্টার" && git push origin main
git add index.html && git commit -m "Add Google Site Name SEO for খেদমত সেন্টার" && git push origin mainmysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SHOW TABLES;"
git diff api.php | head -50
git add api.php && git commit -m "Category filter fix - cats JSON field check" && git push origin main
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "
CREATE TABLE IF NOT EXISTS visitors (
  id VARCHAR(64) PRIMARY KEY,
  first_visit BIGINT,
  last_visit BIGINT,
  visit_count INT DEFAULT 1
);
"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "DESCRIBE visitors;"
grep -n "visitor\|newReturning" api.php
sed -n '530,560p' api.php
grep -n "switch\|case '" api.php | head -20
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "
CREATE TABLE IF NOT EXISTS visitor_logs (
  visitor_id VARCHAR(64) PRIMARY KEY,
  first_visit BIGINT,
  last_visit BIGINT,
  visit_count INT DEFAULT 1
);
"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "DESCRIBE visitor_logs;"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "
ALTER TABLE visitor_logs ADD COLUMN source VARCHAR(50) DEFAULT 'direct';
"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "DESCRIBE visitor_logs;"
cat > /tmp/visitor_api.py << 'PYEOF'
path = '/home/khedmotcenter/htdocs/khedmotcenter.com/api.php'
c = open(path, encoding='utf-8').read()

marker = "// GET PUBLIC NEWS (masked)"

new_code = '''// VISITOR TRACKING
if ($action === 'track_visitor') {
    $vid = $body['visitor_id'] ?? '';
    $source = $body['source'] ?? 'direct';
    if (!$vid) { echo json_encode(['success'=>false]); exit(); }
    $now = time() * 1000;
    $check = $db->prepare("SELECT visitor_id FROM visitor_logs WHERE visitor_id=?");
    $check->bind_param('s', $vid);
    $check->execute();
    $isNew = $check->get_result()->num_rows === 0;
    $stmt = $db->prepare("INSERT INTO visitor_logs (visitor_id, first_visit, last_visit, visit_count, source) VALUES (?,?,?,1,?) ON DUPLICATE KEY UPDATE last_visit=?, visit_count=visit_count+1");
    $stmt->bind_param('siisi', $vid, $now, $now, $source, $now);
    $stmt->execute();
    echo json_encode(['success'=>true, 'isNew'=>$isNew]);
    exit();
}
// GET VISITOR STATS (ADMIN ONLY)
if ($action === 'get_visitor_stats') {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
        exit();
    }
    $now = time();
    $todayStart = strtotime('today') * 1000;
    $yesterdayStart = strtotime('yesterday') * 1000;
    $sevenDaysStart = ($now - 7*86400) * 1000;

    function countVisitors($db, $since, $until, $isNew) {
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
    }

    $nowMs = $now * 1000;
    $todayNew = countVisitors($db, $todayStart, $nowMs, true);
    $todayReturning = countVisitors($db, $todayStart, $nowMs, false);
    $yestNew = countVisitors($db, $yesterdayStart, $todayStart, true);
    $yestReturning = countVisitors($db, $yesterdayStart, $todayStart, false);
    $weekNew = countVisitors($db, $sevenDaysStart, $nowMs, true);
    $weekReturning = countVisitors($db, $sevenDaysStart, $nowMs, false);

    $totalAll = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs")->fetch_assoc()['cnt'];
    $totalReturning = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE visit_count>1")->fetch_assoc()['cnt'];
    $totalNew = $totalAll - $totalReturning;

    $sourceRows = $db->query("SELECT source, COUNT(*) as cnt FROM visitor_logs GROUP BY source ORDER BY cnt DESC")->fetch_all(MYSQLI_ASSOC);
    $sources = [];
    foreach ($sourceRows as $row) { $sources[$row['source']] = (int)$row['cnt']; }

    echo json_encode([
        'success'=>true,
        'today_new'=>$todayNew, 'today_returning'=>$todayReturning,
        'yesterday_new'=>$yestNew, 'yesterday_returning'=>$yestReturning,
        'week_new'=>$weekNew, 'week_returning'=>$weekReturning,
        'total'=>$totalAll, 'total_new'=>$totalNew, 'total_returning'=>$totalReturning,
        'sources'=>$sources
    ]);
    exit();
}
'''

if marker in c:
    c = c.replace(marker, new_code + marker)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Marker not found!")
PYEOF

python3 /tmp/visitor_api.py
grep -n "GET PUBLIC NEWS" api.php
grep -n "get_public_news" api.php
python3 /tmp/visitor_api2.py
cat > /tmp/visitor_api2.py << 'PYEOF'
path = '/home/khedmotcenter/htdocs/khedmotcenter.com/api.php'
lines = open(path, encoding='utf-8').readlines()

new_code = '''// VISITOR TRACKING
if ($action === 'track_visitor') {
    $vid = $body['visitor_id'] ?? '';
    $source = $body['source'] ?? 'direct';
    if (!$vid) { echo json_encode(['success'=>false]); exit(); }
    $now = time() * 1000;
    $check = $db->prepare("SELECT visitor_id FROM visitor_logs WHERE visitor_id=?");
    $check->bind_param('s', $vid);
    $check->execute();
    $isNew = $check->get_result()->num_rows === 0;
    $stmt = $db->prepare("INSERT INTO visitor_logs (visitor_id, first_visit, last_visit, visit_count, source) VALUES (?,?,?,1,?) ON DUPLICATE KEY UPDATE last_visit=?, visit_count=visit_count+1");
    $stmt->bind_param('siisi', $vid, $now, $now, $source, $now);
    $stmt->execute();
    echo json_encode(['success'=>true, 'isNew'=>$isNew]);
    exit();
}
// GET VISITOR STATS (ADMIN ONLY)
if ($action === 'get_visitor_stats') {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
        exit();
    }
    $now = time();
    $todayStart = strtotime('today') * 1000;
    $yesterdayStart = strtotime('yesterday') * 1000;
    $sevenDaysStart = ($now - 7*86400) * 1000;

    function countVisitors($db, $since, $until, $isNew) {
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
    }

    $nowMs = $now * 1000;
    $todayNew = countVisitors($db, $todayStart, $nowMs, true);
    $todayReturning = countVisitors($db, $todayStart, $nowMs, false);
    $yestNew = countVisitors($db, $yesterdayStart, $todayStart, true);
    $yestReturning = countVisitors($db, $yesterdayStart, $todayStart, false);
    $weekNew = countVisitors($db, $sevenDaysStart, $nowMs, true);
    $weekReturning = countVisitors($db, $sevenDaysStart, $nowMs, false);

    $totalAll = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs")->fetch_assoc()['cnt'];
    $totalReturning = (int)$db->query("SELECT COUNT(*) as cnt FROM visitor_logs WHERE visit_count>1")->fetch_assoc()['cnt'];
    $totalNew = $totalAll - $totalReturning;

    $sourceRows = $db->query("SELECT source, COUNT(*) as cnt FROM visitor_logs GROUP BY source ORDER BY cnt DESC")->fetch_all(MYSQLI_ASSOC);
    $sources = [];
    foreach ($sourceRows as $row) { $sources[$row['source']] = (int)$row['cnt']; }

    echo json_encode([
        'success'=>true,
        'today_new'=>$todayNew, 'today_returning'=>$todayReturning,
        'yesterday_new'=>$yestNew, 'yesterday_returning'=>$yestReturning,
        'week_new'=>$weekNew, 'week_returning'=>$weekReturning,
        'total'=>$totalAll, 'total_new'=>$totalNew, 'total_returning'=>$totalReturning,
        'sources'=>$sources
    ]);
    exit();
}
'''

lines.insert(172, new_code)
open(path, 'w', encoding='utf-8').writelines(lines)
print("Done!")
PYEOF

python3 /tmp/visitor_api2.py
php -l api.php
curl -s "http://localhost:8080/api.php" -X POST -H "Content-Type: application/json" -d '{"action":"track_visitor","visitor_id":"test123","source":"google"}'
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# curl -s "http://localhost:8080/api.php" -X POST -H "Content-Type: application/json" -d '{"action":"track_visitor","visitor_id":"test123","source":"google"}'"test123","source":"google"}'
{"success":true,"isNew":false}root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
77:   ══════════════════════════════════════ */
78:
79:   /* ── HERO ── */
80:   .hero {
81:     background: linear-gradient(180deg, #FAFBFF 0%, #F0F2FA 100%);
82:     padding: 1.6rem 1rem 1.2rem;
83:     position: relative; overflow: hidden;
84:   }
85:   .hero-card { max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 24px; padding: 2rem 1.5rem 1.8rem; text-align: center; position:
86:   .hero-card::before { content: ''; position: absolute; inset: 0; background-image: radial-gradient(circle at 15% 20%, rgba(10,124,62,0.05) 0%, transp
87:   .hero-card::after { content: ''; position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 70%; height: 3px; border-radius: 0 0 6px
88:   .hero-inner { position: relative; z-index: 1; }
89:   .hero-main-title {
90:     font-family
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# curl -s "http://localhost:8080/api.php" -X POST -H "Content-Type: application/json" -d '{"action":"track_visitor","visitor_id":"test123","source":"google"}'"test123","source":"google"}'
{"success":true,"isNew":false}root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
grep -n "limit: 10\|limit: 20\|limit: 50\|loadMoreNews\|newsOffset\|_offset" index.html | head -30
cd /home/khedmotcenter/htdocs/khedmotcenter.com && grep -n "limit: 10\|limit: 20\|limit: 50\|loadMoreNews\|newsOffset\|_offset" index.html | head -30
grep -n "admLoadPosts\|loadUserDashboard\|postsContainer\|udMainContent" index.html | head -20
sed -n '1642,1660p' index.html
sed -n '1536,1560p' index.html
grep -n "admin_get_posts" api.php
sed -n '345,370p' api.php
python3 << 'PYEOF'
content = open('api.php', 'r').read()
old = '''if ($action === 'admin_get_posts') {
    $rows  = $db->query("SELECT * FROM posts ORDER BY created DESC")->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'posts' => $posts]);
    exit();
}'''
new = '''if ($action === 'admin_get_posts') {
    $limit  = intval($body['limit'] ?? 10);
    $offset = intval($body['offset'] ?? 0);
    $cat    = $body['category'] ?? '';
    $where  = "WHERE status='active'";
    if ($cat) { $where = "WHERE status='active' AND (category='$cat' OR cats LIKE '%$cat%')"; }
    $total_stmt = $db->query("SELECT COUNT(*) as cnt FROM posts");
    $total = (int)$total_stmt->fetch_assoc()['cnt'];
    $stmt = $db->prepare("SELECT * FROM posts ORDER BY created DESC LIMIT ? OFFSET ?");
    $stmt->bind_param('ii', $limit, $offset);
    $stmt->execute();
    $rows  = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $posts = array_map(fn($r) => formatPost($r, false), $rows);
    echo json_encode(['success' => true, 'posts' => $posts, 'total' => $total]);
    exit();
}'''
if old in content:
    content = content.replace(old, new)
    open('api.php', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

php -l api.php
sed -n '1642,1648p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()
old = "  const res = await api('admin_get_posts');\n  load72hBar('postsContainer');"
new = "  const res = await api('admin_get_posts', {limit: 10, offset: window._admOffset || 0});\n  load72hBar('postsContainer');"
if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

oot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# python3 << 'PYEOF'
content = open('index.html', 'r').read()
old = "  const res = await api('admin_get_posts');\n  load72hBar('postsContainer');"
new = "  const res = await api('admin_get_posts', {limit: 10, offset: window._admOffset || 0});\n  load72hBar('postsContainer');"
if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

Done
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
sed -n '1660,1672p' index.html
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "DELETE FROM visitor_logs WHERE visitor_id='test123';"
grep -n "tab-btn\|admSwitchTab\|tabPosts\|tabUsers" index.html | head -20
pwd
cd /home/khedmotcenter/htdocs/khedmotcenter.com
cat > /tmp/visitor_html.py << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

old_tabs = '''      <button class="tab-btn active" onclick="admSwitchTab('posts')">\U0001f4f0 \u09aa\u09cb\u09b8\u09cd\u099f \u09ae\u09cd\u09af\u09be\u09a8\u09c7\u099c\u09ae\u09c7\u09a8\u09cd\u099f</button>
      <button class="tab-btn" onclick="admSwitchTab('users')">\U0001f465
\u0987\u0989\u099c\u09be\u09b0 \u09ae\u09cd\u09af\u09be\u09a8\u09c7\u099c\u09ae\u09c7\u09a8\u09cd\u099f</button>'''

new_tabs = '''      <button class="tab-btn active" onclick="admSwitchTab('posts')">\U0001f4f0 \u09aa\u09cb\u09b8\u09cd\u099f \u09ae\u09cd\u09af\u09be\u09a8\u09c7\u099c\u09ae\u09c7\u09a8\u09cd\u099f</button>
      <button class="tab-btn" onclick="admSwitchTab('users')">\U0001f465 \u0987\u0989\u099c\u09be\u09b0 \u09ae\u09cd\u09af\u09be\u09a8\u09c7\u099c\u09ae\u09c7\u09a8\u09cd\u099f</button>
      <button class="tab-btn" onclick="admSwitchTab('visitors')">\U0001f4ca \u09ad\u09bf\u099c\u09bf\u09a4\u09b0 \u098f\u09a8\u09be\u09b2\u09be\u09b8\u09bf\u09b8</button>'''

if old_tabs in c:
    c = c.replace(old_tabs, new_tabs)
    print("Tabs added!")
else:
    print("Tabs NOT found!")

open(path, 'w', encoding='utf-8').write(c)
PYEOF

python3 /tmp/visitor_html.py
sed -n '870,873p' index.html
grep -n "admSwitchTab" index.html
cat > /tmp/visitor_html2.py << 'PYEOF'
path = 'index.html'
lines = open(path, encoding='utf-8').readlines()

new_tab_btn = '      <button class="tab-btn" onclick="admSwitchTab(\'visitors\')">\U0001f4ca \u09ad\u09bf\u099c\u09bf\u09a4\u09b0 \u098f\u09a8\u09be\u09b2\u09be\u09b8\u09bf\u09b8</button>\n'

# লাইন 1026 এর পরে insert (0-indexed: 1026)
lines.insert(1026, new_tab_btn)
open(path, 'w', encoding='utf-8').writelines(lines)
print("Done!")
PYEOF

python3 /tmp/visitor_html2.py
grep -n "admSwitchTab" index.html
sed -i "s/ভিজিতর এনালাসিস/ভিজিটর এনালাইসিস/" index.html && grep -n "ভিজিটর" index.html
sed -n '1600,1610p' index.html
grep -n 'id="tabUsers"' index.html
sed -n '1078,1110p' index.html
cat > /tmp/visitor_panel.py << 'PYEOF'
path = 'index.html'
lines = open(path, encoding='utf-8').readlines()

panel = '''    <div class="tab-panel" id="tabVisitors">
      <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-header-icon">\U0001f4ca</div><div class="adm-card-header-title">\u09ad\u09bf\u099c\u09bf\u099f\u09b0 \u09b8\u09cd\u099f\u09cd\u09af\u09be\u099f\u09b8</div></div>
        <div class="adm-card-body" id="visitorStatsContainer">
          <div class="adm-empty">\u09b2\u09cb\u09a1 \u09b9\u099a\u09cd\u099b\u09c7\u2026</div>
        </div>
      </div>
    </div>
'''

# find line with id="tabUsers" closing, insert after it (line index 1090, 0-indexed for line 1091)
for i, line in enumerate(lines):
    if i >= 1078 and line.strip() == '</div>' and lines[i-1].strip().startswith('<div class="adm-card-body"><div id="usersContainer"'):
        insert_at = i + 1
        break
else:
    insert_at = None

if insert_at:
    lines.insert(insert_at, panel)
    open(path, 'w', encoding='utf-8').writelines(lines)
    print(f"Inserted at line {insert_at}")
else:
    print("Position not found, manual check needed")
PYEOF

python3 /tmp/visitor_panel.py
sed -n '1088,1100p' index.html
sed -n '1100,1108p' index.html
sed -n '1090,1102p' index.html
python3 -c "
path = 'index.html'
lines = open(path, encoding='utf-8').readlines()
# 1092 নম্বর লাইনের আগে একটা </div> যোগ করি (0-indexed: 1091)
lines.insert(1091, '    </div>\n')
open(path, 'w', encoding='utf-8').writelines(lines)
print('Done!')
"
sed -n '1088,1105p' index.html
sed -n '1655,1665p' index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && sed -n '1655,1665p' index.html
grep -n "^}" index.html | awk -F: '$1>1657 && $1<1680'
sed -n '1664,1668p' index.html
sed -i "1666a\\  window._admOffset = (window._admOffset || 0) + res.posts.length;\n  if (res.posts.length >= 10) {\n    const admMore = document.createElement('div');\n    admMore.id = 'admLoadMore';\n    admMore.style = 'text-align:center;padding:1rem;';\n    admMore.innerHTML = '<button onclick=\"admLoadMorePosts()\" style=\"background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;\">খেদমতের খবর আরো দেখুন</button>';\n    c.appendChild(admMore);\n  }" index.html
sed -i '1674a\async function admLoadMorePosts() { const lm = document.getElementById("admLoadMore"); if (lm) lm.remove(); const btn = document.querySelector("#admLoadMore button"); if (btn) btn.textContent = "লোড হচ্ছে..."; const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10}); if (!res.success || !res.posts.length) return; const c = document.getElementById("postsContainer"); res.posts.forEach(p => { const div = document.createElement("div"); div.className = "post-item"; div.id = "postItem"+p.id; div.innerHTML = "<div class=adm-post-text>"+p.content+"</div>"; c.appendChild(div); }); window._admOffset = (window._admOffset || 10) + res.posts.length; if (res.posts.length >= 20) { const more = document.createElement("div"); more.id = "admLoadMore"; more.style = "text-align:center;padding:1rem;"; more.innerHTML = "<button onclick=admLoadMorePosts() style=background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;>খেদমতের খবর আরো দেখুন</button>"; c.appendChild(more); } }' index.html
echo "Done"
git add index.html api.php && git commit -m "feat: pagination 10+20, category filter fix, copy protection, admin pagination" && git push origin main && echo "Push done"
grep -n "get_user_dashboard" api.php
sed -n '1655,1665p' index.html
oot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# sed -n
'1655,1665p' index.html
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
python3 << 'PYEOF'
path = 'index.html'
lines = open(path, encoding='utf-8').readlines()

# 1089-1101 replace (0-indexed: 1088-1100)
new_section = '''      <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-header-icon">👥</div><div class="adm-card-header-title">সকল ইউজারসমূহ</div></div>
        <div class="adm-card-body"><div id="usersContainer"><div class="adm-empty">লোড হচ্ছে…</div></div></div>
      </div>
    </div>
    <div class="tab-panel" id="tabVisitors">
      <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-header-icon">📊</div><div class="adm-card-header-title">ভিজিটর এনালাইসিস</div></div>
        <div class="adm-card-body" id="visitorStatsContainer">
          <div class="adm-empty">লোড হচ্ছে…</div>
        </div>
      </div>
    </div>
'''

lines[1088:1101] = [new_section]
open(path, 'w', encoding='utf-8').writelines(lines)
print("Done!")
PYEOF

cd /home/khedmotcenter/htdocs/khedmotcenter.com && sed -n '1088,1105p' index.html
cp index.html index.html.before-visitor-fix && tar -czf /root/backup_before_visitor.tar.gz index.html api.php
grep -n '<div class="tabs">' index.html
sed -n '1024p;1100,1110p' index.html
cat > /tmp/fix_tabs.py << 'PYEOF'
path = 'index.html'
lines = open(path, encoding='utf-8').readlines()

# Complete new tabs + panels section (সম্পূর্ণ সঠিক HTML structure)
new_tabs_section = '''    <div class="tabs">
      <button class="tab-btn active" onclick="admSwitchTab('posts')">📰 পোস্ট ম্যানেজমেন্ট</button>
      <button class="tab-btn" onclick="admSwitchTab('users')">👥 ইউজার ম্যানেজমেন্ট</button>
      <button class="tab-btn" onclick="admSwitchTab('visitors')">📊 ভিজিটর এনালাইসিস</button>
    </div>
    <div class="tab-panel active" id="tabPosts">
      <div class="adm-card">
        <div class="adm-card-header"><div class="adm-card-header-icon">✏️</div><div class="adm-card-header-title">নতুন পোস্ট করুন</div></div>
        <div class="adm-card-body">
          <div id="spStep1">
            <textarea id="postContent" rows="10" style="width:100%;padding:0.9rem;font-size:0.92rem;font-family:inherit;border:1.5px solid rgba(5,5,125,0.2);border-radius:14px;resize:vertical;outline:none;line-height:1.8;color:#1a1d3a;box-sizing:border-box;" placeholder="এখানে নিয়োগ বিজ্ঞপ্তি পেস্ট করুন..."></textarea>
            <div style="margin-top:1rem;">
              <label style="font-size:0.88rem;font-weight:700;color:#05057D;display:block;margin-bottom:0.5rem;">ক্যাটাগরি <span style="color:red">*</span></label>
              <div id="admCatError" style="display:none;color:red;font-size:0.78rem;margin-bottom:0.3rem;">অন্তত একটি ক্যাটাগরি সিলেক্ট করুন।</div>
              <div style="display:flex;flex-w
python3 /tmp/fix_tabs.py
echo $?

cd /home/khedmotcenter/htdocs/khedmotcenter.com && php -l index.html
grep -n "admSwitchTab" index.html | head -10
sed -n '1610,1615p' index.html
python3 << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

old_fn = """function admSwitchTab(tab) {
  document.querySelectorAll('#pageAdminDashboard .tab-btn').forEach((b,i)=>b.classList.toggle('active',(i===0&&tab==='posts')||(i===1&&tab==='users')));
  document.getElementById('tabPosts').classList.toggle('active', tab==='posts');
  document.getElementById('tabUsers').classList.toggle('active', tab==='users');
  if (tab==='users') admLoadUsers();
}"""

new_fn = """function admSwitchTab(tab) {
  document.querySelectorAll('#pageAdminDashboard .tab-btn').forEach((b,i)=>b.classList.toggle('active',(i===0&&tab==='posts')||(i===1&&tab==='users')||(i===2&&tab==='visitors')));
  document.getElementById('tabPosts').classList.toggle('active', tab==='posts');
  document.getElementById('tabUsers').classList.toggle('active', tab==='users');
  document.getElementById('tabVisitors').classList.toggle('active', tab==='visitors');
  if (tab==='users') admLoadUsers();
  if (tab==='visitors') loadVisitorStats();
}"""

if old_fn in c:
    c = c.replace(old_fn, new_fn)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

cat > /tmp/visitor_js.py << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

marker = "let admImgBase64 = null;"

new_funcs = """async function loadVisitorStats() {
  const res = await api('get_visitor_stats');
  const cont = document.getElementById('visitorStatsContainer');
  if (!res.success) { cont.innerHTML = '<div class="adm-empty">'+res.message+'</div>'; return; }
  const h = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem;">
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.today_new}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">আজকের নতুন ভিজিটর</div>
      </div>
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.today_returning}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">আজকের রিটার্নিং ভিজিটর</div>
      </div>
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.yesterday_new}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">গতকালের নতুন ভিজিটর</div>
      </div>
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.yesterday_returning}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">গতকালের রিটার্নিং ভিজিটর</div>
      </div>
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.week_new}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">গত ৭ দিনের নতুন ভিজিটর</div>
      </div>
      <div style="background:rgba(5,5,125,0.05);border-radius:12px;padding:1rem;text-align:center;">
        <div style="font-size:2rem;font-weight:700;color:#05057D;">${res.week_returning}</div>
        <div style="font-size:0.85rem;color:#666;margin-top:0.3rem;">গত ৭ দিনের রিটার্নিং ভিজিটর</div>
      </div>
      <div style="background:linear-gradient(135deg,#05057D,#0A0A9E);border-radius:12px;padding:1rem;text-align:center;color:#fff;">
        <div style="font-size:2rem;font-weight:700;color:#FFD700;">${res.total}</div>
        <div style="font-size:0.85rem;color:#fff;margin-top:0.3rem;opacity:0.9;">মোট ভিজিটর</div>
      </div>
      <div style="background:linear-gradient(135deg,#05057D,#0A0A9E);border-radius:12px;padding:1rem;text-align:center;color:#fff;">
        <div style="font-size:2rem;font-weight:700;color:#FFD700;">${res.total_new}</div>
        <div style="font-size:0.85rem;color:#fff;margin-top:0.3rem;opacity:0.9;">মোট নতুন ভিজিটর</div>
      </div>
      <div style="background:linear-gradient(135deg,#05057D,#0A0A9E);border-radius:12px;padding:1rem;text-align:center;color:#fff;">
        <div style="font-size:2rem;font-weight:700;color:#FFD700;">${res.total_returning}</div>
        <div style="font-size:0.85rem;color:#fff;margin-top:0.3rem;opacity:0.9;">মোট রিটার্নিং ভিজিটর</div>
      </div>
    </div>
    <div class="adm-card" style="margin-top:1.5rem;">
      <div class="adm-card-header"><div class="adm-card-header-icon">🌐</div><div class="adm-card-header-title">ট্রাফিক সোর্স</div></div>
      <div class="adm-card-body">
        ${Object.entries(res.sources || {}).map(([src,cnt])=>`<div style="display:flex;justify-content:space-between;padding:0.6rem 0;border-bottom:1px solid rgba(0,0,0,0.05);"><span>${src}</span><strong>${cnt}</strong></div>`).join('')}
      </div>
    </div>
  `;
  cont.innerHTML = h;
}
async function trackVisitor() {
  let vid = localStorage.getItem('visitor_id');
  if (!vid) { vid = 'v_' + Date.now() + '_' + Math.random().toString(36).substr(2,9); localStorage.setItem('visitor_id', vid); }
  let source = 'direct';
  const params = new URLSearchParams(window.location.search);
  if (params.get('utm_source')) source = params.get('utm_source');
  else if (document.referrer.includes('google')) source = 'google';
  else if (document.referrer.includes('facebook')) source = 'facebook';
  else if (document.referrer.includes('whatsapp')) source = 'whatsapp';
  await api('track_visitor', {visitor_id: vid, source: source});
}
"""

if marker in c:
    c = c.replace(marker, new_funcs + '\n' + marker)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Marker not found!")
PYEOF

python3 /tmp/visitor_js.py
python3 << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

# </body> এর আগে script যোগ করি
old = '</body>'
new = '''<script>
document.addEventListener('DOMContentLoaded', trackVisitor);
</script>
</body>'''

if old in c:
    c = c.replace(old, new)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

php -l index.html && echo "✓ HTML OK"
git add index.html api.php && git commit -m "Add Visitor Analytics - Admin Dashboard with stats and tracking" && git push origin main
tar -czf /root/khedmotcenter_backup_$(date +%Y%m%d_%H%M).tar.gz . && echo "Backup Done!"
grep -n "adminToken" index.html | head -5
python3 << 'PYEOF'
path = 'api.php'
c = open(path, encoding='utf-8').read()

old_check = """if ($action === 'get_visitor_stats') {
    session_start();
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
        exit();
    }"""

new_check = """if ($action === 'get_visitor_stats') {
    $token = $body['token'] ?? '';
    if ($token !== 'admin_session') {
        echo json_encode(['success'=>false, 'message'=>'Unauthorized']);
        exit();
    }"""

if old_check in c:
    c = c.replace(old_check, new_check)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

php -l api.php
git add api.php && git commit -m "Fix visitor stats auth - use token instead of session" && git push origin main
git add . && git commit -m "Visitor Analytics System Complete" && git push origin main
sed -n '280,330p' api.php
grep -n "if.*action.*admin_create_post" api.php
sed -n '363,420p' api.php
$dup = $db->prepare("SELECT id FROM posts WHERE text=? LIMIT 1");
cat > /tmp/undo_dup.py << 'PYEOF'
path = 'api.php'
c = open(path, encoding='utf-8').read()

old_dup = '''    // Duplicate check: Position + Address + Content Similarity (70-80%) + Last 5 days
    $since_5days = (time() - 5 * 86400) * 1000;
    $dup_stmt = $db->prepare("SELECT id, text FROM posts WHERE position=? AND address=? AND created>=? AND status='active'");
    $dup_stmt->bind_param('ssi', $position, $address, $since_5days);
    $dup_stmt->execute();
    $dup_result = $dup_stmt->get_result();
    
    if ($dup_result->num_rows > 0) {
        while ($dup_row = $dup_result->fetch_assoc()) {
            $existing_text = $dup_row['text'];
            $sim = 0;
            similar_text($text, $existing_text, $sim);
            if ($sim >= 70) {
                echo json_encode(['success' => false, 'message' => '⚠️ এই পদের জন্য এই ঠিকানায় একই ধরনের পোস্ট ৫ দিনের মধ্যে আছে! ('.$sim.'% মিল)']); exit();
            }
        }
    }'''

new_dup = '''    $dup = $db->prepare("SELECT id FROM posts WHERE text=? LIMIT 1");
    $dup->bind_param('s', $text);
    $dup->execute();
    $dup->store_result();
    if ($dup->num_rows > 0) { echo json_encode(['success' => false, 'message' => '⚠️ এই পোস্টটি আগেই করা হয়েছে!']); exit(); }'''

if old_dup in c:
    c = c.replace(old_dup, new_dup)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

python3 /tmp/undo_dup.py
grep -n "Duplicate check: Position" api.php
cat > /tmp/realtime_dup.py << 'PYEOF'
path = 'index.html'
c = open(path, encoding='utf-8').read()

marker = "document.getElementById('postContent').addEventListener('paste'"

new_code = '''async function checkDuplicate() {
  const content = document.getElementById('postContent').value.trim();
  const position = document.getElementById('postPosition').value.trim();
  const address = document.getElementById('postAddress').value.trim();
  
  if (!content || !position || !address) return;
  
  const res = await api('check_duplicate', {content, position, address});
  const alert = document.getElementById('postAlert');
  
  if (res.duplicate) {
    alert.style.display = 'block';
    alert.className = 'alert alert-warning';
    alert.innerHTML = '⚠️ সতর্কতা: এই পদ ও ঠিকানায় ৫ দিনের মধ্যে '+res.similarity+'% মিলে এমন পোস্ট আছে!';
    document.querySelector('button[onclick="spShowPreview()"]').disabled = true;
  } else if (res.message) {
    alert.style.display = 'block';
    alert.className = 'alert';
    alert.innerHTML = res.message;
    document.querySelector('button[onclick="spShowPreview()"]').disabled = false;
  } else {
    alert.style.display = 'none';
    document.querySelector('button[onclick="spShowPreview()"]').disabled = false;
  }
}

document.getElementById('postContent').addEventListener('paste', function(e) {
  setTimeout(checkDuplicate, 100);
});
document.getElementById('postContent').addEventListener('input', function(e) {
  if (e.target.value.length % 20 === 0) checkDuplicate();
});
document.getElementById('postPosition').addEventListener('input', checkDuplicate);
document.getElementById('postAddress').addEventListener('input', checkDuplicate);

document.getElementById('postContent').addEventListener('paste'''

if marker in c:
    c = c.replace(marker, new_code + marker)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

python3 /tmp/realtime_dup.py
cat > /tmp/check_dup_api.py << 'PYEOF'
path = 'api.php'
c = open(path, encoding='utf-8').read()

marker = "if ($action === 'admin_create_post')"

new_action = '''// CHECK DUPLICATE (Real-time)
if ($action === 'check_duplicate') {
    $content = $body['content'] ?? '';
    $position = $body['position'] ?? '';
    $address = $body['address'] ?? '';
    
    if (!$content || !$position || !$address) {
        echo json_encode(['success'=>true, 'duplicate'=>false]);
        exit();
    }
    
    $since_5days = (time() - 5 * 86400) * 1000;
    $stmt = $db->prepare("SELECT text FROM posts WHERE position=? AND address=? AND created>=? AND status='active'");
    $stmt->bind_param('ssi', $position, $address, $since_5days);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $max_similarity = 0;
    while ($row = $result->fetch_assoc()) {
        $sim = 0;
        similar_text($content, $row['text'], $sim);
        if ($sim > $max_similarity) $max_similarity = $sim;
        if ($sim >= 70) break;
    }
    
    echo json_encode([
        'success'=>true,
        'duplicate'=>($max_similarity >= 70),
        'similarity'=>round($max_similarity)
    ]);
    exit();
}

if ($action === 'admin_create_post')'''

if marker in c:
    c = c.replace(marker, new_action)
    open(path, 'w', encoding='utf-8').write(c)
    print("Done!")
else:
    print("Not found!")
PYEOF

python3 /tmp/check_dup_api.py
php -l api.php && php -l index.html
oot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# php -l
api.php && php -l index.html
No syntax errors detected in api.php
No syntax errors detected in index.html
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
git add index.html api.php && git commit -m "Add Real-time Duplicate Check" && git push origin main
ls -lh /root/khedmotcenter_backup_*.tar.gz | tail -3
ls -lh /root/khedmotcenter_backup_*.tar.gz
ls -lh /root/backup_before_visitor.tar.gz /root/khedmotcenter_backup_20260620*.tar.gz 2>/dev/null || echo "Not found in /root"
tar -czf /root/khedmotcenter_backup_$(date +%Y%m%d_%H%M).tar.gz . && echo "Done!" && ls -lh /root/khedmotcenter_backup_20260620*.tar.gz
grep -n "disabled\|block" index.html | grep -i "button\|style" | head -20
cd /home/khedmotcenter/htdocs/ && tar -xzf /root/backup_before_visitor.tar.gz && cd khedmotcenter.com && echo "Restored!"
curl -s "http://localhost:8080/" | head -20
git log --oneline | head -5
git revert HEAD --no-edit && git push origin main
sed -n '263,295p' api.php
cd /home/khedmotcenter/htdocs/khedmotcenter.com && sed -n '263,295p' api.php
sed -n '1536,1560p' index.html
sed -n '1560,1580p' index.html
grep -n "applyFilter" index.html
sed -n '1298,1340p' index.html
sed -n '1562,1570p' index.html
python3 << 'PYEOF'
content = open('api.php', 'r').read()
old = "    $params[] = 50;\n    $types   .= 'i';\n    $stmt2 = $db->prepare(\"SELECT * FROM posts $where ORDER BY created DESC LIMIT ?\");"
new = "    $limit2 = intval($body['limit'] ?? 10);\n    $offset2 = intval($body['offset'] ?? 0);\n    $params[] = $limit2;\n    $params[] = $offset2;\n    $types   .= 'ii';\n    $stmt2 = $db->prepare(\"SELECT * FROM posts $where ORDER BY created DESC LIMIT ? OFFSET ?\");"
if old in content:
    content = content.replace(old, new)
    open('api.php', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

php -l api.php
python3 << 'PYEOF'
content = open('index.html', 'r').read()
old = "  load72hBar('udMainContent');\n}\nasync function applyFilterUD"
new = """  window._udOffset = posts.length;
  if (posts.length >= 10) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id=\"udLoadMore\" style=\"text-align:center;padding:1rem;\"><button onclick=\"udLoadMorePosts()\" style=\"background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;\">খেদমতের খবর আরো দেখুন</button></div>');
  }
  load72hBar('udMainContent');
}
async function udLoadMorePosts() {
  const btn = document.querySelector('#udLoadMore button');
  if (btn) btn.textContent = 'লোড হচ্ছে...';
  const res = await api('get_user_dashboard', {mobile: window._userMobile || '', limit: 20, offset: window._udOffset || 10});
  if (!res.success || !res.posts.length) { const lm = document.getElementById('udLoadMore'); if (lm) lm.remove(); return; }
  const lm = document.getElementById('udLoadMore');
  if (lm) lm.remove();
  const catMap = {'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'};
  const html = res.posts.map(p => {
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap[c]||c}</span>`).join('');
    return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${catTags?`<div style="margin:0.3rem 0.8rem;">${catTags}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
  }).join('');
  document.getElementById('udMainContent').insertAdjacentHTML('beforeend', html);
  window._udOffset = (window._udOffset || 10) + res.posts.length;
  if (res.posts.length >= 20) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id=\"udLoadMore\" style=\"text-align:center;padding:1rem;\"><button onclick=\"udLoadMorePosts()\" style=\"background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;\">খেদমতের খবর আরো দেখুন</button></div>');
  }
}
async function applyFilterUD"""
if "  load72hBar('udMainContent');\n}\nasync function applyFilterUD" in content:
    content = content.replace("  load72hBar('udMainContent');\n}\nasync function applyFilterUD", new)
    open('index.html', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

grep -n "udLoadMore\|udLoadMorePosts\|_udOffset" index.html | head -10
git add index.html api.php && git commit -m "feat: user dashboard pagination with load more button" && git push origin main && echo "Push done"
grep -n "admLoadMorePosts\|admLoadMore" index.html
/home/khedmotcenter/htdocs/khedmotcenter.com/favicon.ico
/home/khedmotcenter/htdocs/khedmotcenter.com/favicon-32.png
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git pull origin main
grep -n "favicon" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -i 's|favicon-v2.png|favicon.png|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "update favicon" && git push origin main
Enumerating objects: 5, done.
Counting objects: 100% (5/5), done.
Compressing objects: 100% (3/3), done.
Writing objects: 100% (3/3), 313 bytes | 313.00 KiB/s, done.
Total 3 (delta 2), reused 0 (delta 0), pack-reused 0
remote: Resolving deltas: 100% (2/2), completed with 2 local objects.To https://github.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-.git
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# file changed, 1 insertion(+), 1 deletion(-)
Enumerating objects: 5, done.
Counting objects: 100% (5/5), done.
Compressing objects: 100% (3/3), done.
Writing objects: 100% (3/3), 313 bytes | 313.00 KiB/s, done.
Total 3 (delta 2), reused 0 (delta 0), pack-reused 0
remote: Resolving deltas: 100% (2/2), completed with 2 local objects.To https://github.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-.git
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
grep -n "green" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -20
sed -i 's/--green: #15803d/--green: #05057D/' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "green to blue" && git push origin main
grep -n "green\|#15803\|#16a34\|#166534\|#22c55" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -30
grep -n "স্বাগতম\|welcome\|hero" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -i 's/#0a7c3e/#05057D/g' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
git add index.html && git commit -m "green to blue hero" && git push origin main
grep -n "mosque\|🕌\|login-icon\|hero-icon\|login.*img\|logo.*img" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -i 's|<div class="ls-logo">🕌</div>|<div class="ls-logo"><img src="favicon.png" style="width:60px;height:60px;object-fit:contain;border-radius:50%;"></div>|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
git add index.html && git commit -m "logo in login page" && git push origin main
sed -i 's|src="favicon.png"|src="logo.png"|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
git pull origin main && git add index.html && git commit -m "update logo" && git push origin main
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git pull origin main && git add index.html && git commit -m "update logo" && git push origin main
grep -n "ls-logo" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -i 's/background: linear-gradient(135deg, #FFD700, #e6c000)/background: #ffffff/' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
git add index.html && git commit -m "white logo bg" && git push origin main
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git pull origin main
grep -n "ls-logo\|admin.*avatar\|admin.*photo\|admin.*img\|لس" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
grep -n "person\|avatar\|profile\|👤\|admin-photo\|admin_photo" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -i 's|<div class="hero-avatar">👤</div>|<div class="hero-avatar"><img src="admin_photo.jpg" style="width:56px;height:56px;object-fit:cover;border-radius:50%;"></div>|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
git add index.html && git commit -m "admin photo added" && git push origin main
sed -n '650,670p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "createdAt\|expiresAt\|একাউন্ট তৈরির\|মেয়াদ শেষ\|expiry_date\|admFmtDate\|fmtDate" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -20
grep -n "formatDateUD" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "created_at\|createdAt\|expiry_date\|expiresAt" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -15
sed -i "s/unset(\$user\['password'\]);/unset(\$user['password']); \$user['created_at'] = \$user['createdAt'] ? date('d\/m\/Y', intval(\$user['createdAt']) \/ 1000) : ''; \$user['expiry_date'] = \$user['expiresAt'] ? date('d\/m\/Y', intval(\$user['expiresAt']) \/ 1000) : '';/" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
grep -n "created_at\|expiry_date" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -10
git add api.php && git commit -m "fix user date fields" && git push origin main
grep -n "Invalid Date\|expiry_date\|expiresAt\|admFmtDate\|fmtDate" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
grep -n "formatDateUD" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -i "s|function formatDateUD(d) { if (document.getElementById('postContent').addEventListener('paste''') return '—'; return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }|function formatDateUD(d) { if (document.getElementById('postContent').addEventListener('paste''') return '—'; if (typeof d === 'string' \&\& d.includes('/')) { const p=d.split('/'); d=p[2]+'-'+p[1]+'-'+p[0]; } return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }|" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "formatDateUD" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -3
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old=\"function formatDateUD(d) { if (document.getElementById('postContent').addEventListener('paste''') return '—'; return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }\"
new=\"function formatDateUD(d) { if (document.getElementById('postContent').addEventListener('paste''') return '—'; if (typeof d==='string' && d.includes('/')) { const p=d.split('/'); d=p[2]+'-'+p[1]+'-'+p[0]; } return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }\"
if old in c:
    c=c.replace(old,new,1)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
"
sed -n '1543p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cat > /tmp/fix.py << 'EOF'
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old="function formatDateUD(d) { if (!d) return '\u2014'; return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }"
new="function formatDateUD(d) { if (!d) return '\u2014'; if (typeof d==='string' && d.includes('/')) { const p=d.split('/'); d=p[2]+'-'+p[1]+'-'+p[0]; } return new Date(d).toLocaleDateString('bn-BD',{year:'numeric',month:'long',day:'numeric'}); }"
if old in c:
    c=c.replace(old,new,1)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
EOF

python3 /tmp/fix.py
git add index.html && git commit -m "fix expiry date" && git push origin main
grep -n "ls-user-name\|ls-user-mobile\|user-header\|user\.name\|user\.mobile" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
cat > /tmp/fix2.py << 'EOF'
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old='<div class="profile-header"><div class="profile-avatar">\U0001f464</div><div><div class="profile-name">${escHtml(user.name)}</div><div class="profile-mobile">\U0001f4f1 ${user.mobile}</div></div></div>'
new='<div class="info-row"><div class="info-icon">\U0001f464</div><div><div class="info-label">\u09a8\u09be\u09ae</div><div class="info-value">${escHtml(user.name)}</div></div></div><div class="info-row"><div class="info-icon">\U0001f4f1</div><div><div class="info-label">\u09ae\u09cb\u09ac\u09be\u0987\u09b2</div><div class="info-value">${user.mobile}</div></div></div>'
if old in c:
    c=c.replace(old,new,1)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
EOF

python3 /tmp/fix2.py
git add index.html && git commit -m "name mobile separate rows" && git push origin main
grep -n "ঠিকানা\|address\|hourglass\|⏳\|মেয়াদ" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
grep -n "user-info\|ud-card\|dashboard.*card\|ঠিকানা\|মেয়াদ শেষ\|expiresAt\|expiry" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | grep -v "form\|label\|input\|adm\|modal\|register" | head -20
grep -n "ud-info\|user-card\|📍\|👤\|📱\|⏳\|ud-" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -20
sed -n '1550,1560p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -i 's|<div class="info-icon">⏳ </div>|<div class="info-icon">📅</div>|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "profile-body\|profile-card" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -i 's|<div class="info-row"><div class="info-icon">👤</div><div><div class="info-label">নাম</div><div class="info-value">${escHtml(user.name)}</div></div></div><div class="info-row"><div class="info-icon">📱</div><div><div class="info-label">মোবাইল</div><div class="info-value">${user.mobile}</div></div></div>\n      <div class="profile-body">|<div class="profile-body"><div class="info-row"><div class="info-icon">👤</div><div><div class="info-label">নাম</div><div class="info-value">${escHtml(user.name)}</div></div></div><div class="info-row"><div class="info-icon">📱</div><div><div class="info-label">মোবাইল</div><div class="info-value">${user.mobile}</div></div></div>|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -n '1550,1558p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()

old = '''    <div class=\"profile-card\">
      <div class=\"info-row\"><div class=\"info-icon\">👤</div><div><div class=\"info-label\">নাম</div><div class=\"info-value\">\${escHtml(user.name)}</div></div></div><div class=\"info-row\"><div class=\"info-icon\">📱</div><div><div class=\"info-label\">মোবাইল</div><div class=\"info-value\">\${user.mobile}</div></div></div>
      <div class=\"profile-body\">'''

new = '''    <div class=\"profile-card\">
      <div class=\"profile-body\">
        <div class=\"info-row\"><div class=\"info-icon\">👤</div><div><div class=\"info-label\">নাম</div><div class=\"info-value\">\${escHtml(user.name)}</div></div></div>
        <div class=\"info-row\"><div class=\"info-icon\">📱</div><div><div class=\"info-label\">মোবাইল</div><div class=\"info-value\">\${user.mobile}</div></div></div>'''

if old in c:
    c=c.replace(old,new,1)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
"
git add index.html && git commit -m "fix profile card layout" && git push origin main
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
c=c.replace('<div class=\"info-icon\">⏳ </div>','<div class=\"info-icon\">📅</div>')
c=c.replace('</div><div class=\"status-icon\">\${is_active?','</div><div class=\"status-icon\">')
with open(f,'w',encoding='utf-8') as fp: fp.write(c)
print('done')
"
grep -n "অবশিষ্ট\|দিন বাকি\|days_left" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
c=c.replace('\${days_left>=0?\"📆\":\"🚫\"}','')
with open(f,'w',encoding='utf-8') as fp: fp.write(c)
print('done')
"
git add index.html && git commit -m "remove calendar emoji" && git push origin main
grep -n "📆\|🚫" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old = \"<div class=\\\"status-icon\\\">\${days_left>=0?'📆':'🚫'}</div>\"
new = '<div class=\"status-icon\"></div>'
if old in c:
    c=c.replace(old,new)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
"
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "remove calendar emoji" && git push origin main
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old = \"<div class=\\\"status-icon\\\">\${days_left>=0?'📆':'🚫'}</div>\"
new = '<div class=\"status-icon\"></div>'
if old in c:
    c=c.replace(old,new)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
"
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old = \"<div class=\\\"status-icon\\\">\${days_left>=0?'📆':'🚫'}</div>\"
new = '<div class=\"status-icon\"></div>'
if old in c:
    c=c.replace(old,new)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
"
python3 /tmp/fix.py << 'EOF'
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f,'r',encoding='utf-8') as fp: c=fp.read()
old = '<div class="info-icon"></div><div><div class="info-label">একাউন্ট তৈরির তারিখ</div>'
new = '<div class="info-icon">⏳</div><div><div class="info-label">একাউন্ট তৈরির তারিখ</div>'
if old in c:
    c=c.replace(old,new)
    with open(f,'w',encoding='utf-8') as fp: fp.write(c)
    print('done')
else:
    print('not found')
EOF

grep -n "একাউন্ট তৈরির তারিখ" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -i 's|<div class="info-icon"></div><div><div class="info-label">একাউন্ট তৈরির তারিখ</div>|<div class="info-icon">⏳</div><div><div class="info-label">একাউন্ট তৈরির তারিখ</div>|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "add emoji to created date" && git push origin main
grep -n "favicon\|logo.png" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -5
cp /home/khedmotcenter/htdocs/khedmotcenter.com/index.html ~/index_backup_$(date +%Y%m%d).html
ls ~/
grep -n "admin_get_users\|userList\|admLoadUsers\|user-card-adm" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
sed -n '1788,1830p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
curl -s https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/index.html | wc -l
python3 /tmp/patch_users.py
python3 << 'EOF'
f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f, 'r', encoding='utf-8') as fp:
    c = fp.read()
print('searchInput found:', 'id="searchInput"' in c)
print('admLoadUsers found:', 'async function admLoadUsers' in c)
print('setUserTab found:', 'setUserTab' in c)
EOF

python3 -c "
c=open('/home/khedmotcenter/htdocs/khedmotcenter.com/index.html','r',encoding='utf-8').read()
print('ok' if 'async function admLoadUsers' in c else 'no')
"
wget -O /tmp/patch_users.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_users.py && python3 /tmp/patch_users.py
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "user filter tabs" && git push origin main
git pull origin main --rebase && git push origin main
grep -n "admin_get_users\|plain_pass\|password" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -10
wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py
ot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py
--2026-06-20 12:44:33--  https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py
Resolving raw.githubusercontent.com (raw.githubusercontent.com)... 2606:50c0:8002::154, 2606:50c0:8001::154, 2606:50c0:8003::154, ...
Connecting to raw.githubusercontent.com (raw.githubusercontent.com)|2606:50c0:8002::154|:443... connected.
HTTP request sent, awaiting response... 404 Not Found
2026-06-20 12:44:33 ERROR 404: Not Found.
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
--2026-06-20 12:44:33--  https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py
Resolving raw.githubusercontent.com (raw.githubusercontent.com)... 2606:50c0:8002::154, 2606:50c0:8001::154, 2606:50c0:8003::154, ...
Connecting to raw.githubusercontent.com (raw.githubusercontent.com)|2606:50c0:8002::154|:443... connected.
HTTP request sent, awaiting response... 404 Not Found
2026-06-20 12:44:33 ERROR 404: Not Found.
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
-bash: ot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#: No such file or directory
--2026-06-20: command not found
-bash: syntax error near unexpected token `('
-bash: syntax error near unexpected token `('
HTTP: command not found
2026-06-20: command not found
-bash: root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#: No such file or directory
root@srv1737072:~#
-bash: :s^croot@srv1737072:~# ot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py: substitution failed
--2026-06-20: command not found
-bash: syntax error near unexpected token `('
-bash: syntax error near unexpected token `('
HTTP: command not found
2026-06-20: command not found
-bash: root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#: No such file or directory
-bash:: command not found
--2026-06-20:: command not found
-bash: syntax error near unexpected token `('
HTTP:: command not found
2026-06-20:: command not found
-bash:: command not found
root@srv1737072:~#: command not found
root@srv1737072:~#
root@srv1737072:~# root@srv1737072:~#
-bash: :s^croot@srv1737072:~# ot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py: substitution failed
--2026-06-20: command not found
-bash: syntax error near unexpected token `('
-bash: syntax error near unexpected token `('
HTTP: command not found
2026-06-20: command not found
-bash: root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#: No such file or directory
-bash:: command not found
--2026-06-20:: command not found
-bash: syntax error near unexpected token `('
HTTP:: command not found
2026-06-20:: command not found
-bash:: command not found
root@srv1737072:~#: command not found
root@srv1737072:~#
root@srv1737072:~#: command not found
-bash:: command not found
--2026-06-20:: command not found
-bash: syntax error near unexpected token `('
HTTP:: command not found
2026-06-20:: command not found
-bash:: command not found
-bash::: command not found
--2026-06-20::: command not found
>
curl -s -o /dev/null -w "%{http_code}" https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py
wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py
grep -n "pending\|plain_pass\|currentUserTab" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
grep -n "pending" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | grep filter
python3 -c "
f='/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c=open(f,'r',encoding='utf-8').read()
if old in c:
    open(f,'w',encoding='utf-8').write(c.replace(old,new,1))
    print('done')
else:
    print('not found')
"
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "fix user tab buttons" && git push origin main
git pull origin main --rebase && git push origin main
curl -s "https://api.github.com/repos/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/contents/" | grep
curl -s "https://api.github.com/repos/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/contents/" | python3 -c "import sys,json; [print(f['name']) for f in json.load(sys.stdin)]"
sed -i 's|<link rel="icon" type="image/png" href="favicon.png">|<link rel="icon" type="image/png" href="logo.png">|' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "favicon to logo.png" && git push origin main
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git pull origin main --rebase && git push origin main
grep -n "returning\|newReturning\|localStorage\|visitor" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | grep -iv "css\|style\|color" | head -20
grep -n "track_visitor\|newReturning\|returning\|new_visitor" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -15
sed -n '174,220p' /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
sed -n '1700,1715p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "trackVisitor" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "DESCRIBE visitor_logs;"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT COUNT(*) as unique_visitors, SUM(visit_count) as total_visits FROM visitor_logs;"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT COUNT(*) as returning FROM visitor_logs WHERE visit_count > 1;"
sed -n '195,215p' /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
wget -O /tmp/pr.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_returning.py && python3 /tmp/pr.py
git add api.php && git commit -m "fix returning visitor count" && git push origin main

grep -n "মসজিদের নিয়োগ\|পুরুষ মাদ্রাস\|মহিলা মাদ্রাস\|stat-box\|cat-stat\|category-count" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | grep -v "api\|catMap\|filter\|jobs\|admin\|madrasa-jobs" | head -20
sed -n '528,548p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()
old = '''async function admLoadMorePosts() { const lm = document.getElementById("admLoadMore"); if (lm) lm.remove(); const btn = document.querySelector("#admLoadMore button"); if (btn) btn.textContent = "লোড হচ্ছে..."; const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10}); if (!res.success || !res.posts.length) return; const c = document.getElementById("postsContainer"); res.posts.forEach(p => { const div = document.createElement("div"); div.className = "post-item"; div.id = "postItem"+p.id; div.innerHTML = "<div class=adm-post-text>"+p.content+"</div>"; c.appendChild(div); }); window._admOffset = (window._admOffset || 10) + res.posts.length; if (res.posts.length >= 20) { const more = document.createElement("div"); more.id = "admLoadMore"; more.style = "text-align:center;padding:1rem;"; more.innerHTML = "<button onclick=admLoadMorePosts() style=background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;>খেদমতের খবর আরো দেখুন</button>"; c.appendChild(more); } }'''
new = '''async function admLoadMorePosts() {
  const lm = document.getElementById("admLoadMore");
  const btn = lm ? lm.querySelector("button") : null;
  if (btn) btn.textContent = "লোড হচ্ছে...";
  const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10});
  if (lm) lm.remove();
  if (!res.success || !res.posts.length) return;
  const c = document.getElementById("postsContainer");
  const catMap = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  res.posts.forEach(p => {
    const div = document.createElement("div");
    div.className = "post-item";
    div.id = "postItem"+p.id;
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c2=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap[c2]||c2}</span>`).join("");
    div.innerHTML = `<div class="post-item-date">${admFmtDt(p.created)}<div class="post-item-actions"><button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button><button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button></div></div><div class="post-item-body">${catTags?catTags+"<br>":""}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}<div class="adm-post-text">${admEsc(p.content)}</div></div>`;
    c.appendChild(div);
  });
  window._admOffset = (window._admOffset || 10) + res.posts.length;
  if (res.posts.length >= 20) {
    const more = document.createElement("div");
    more.id = "admLoadMore";
    more.style = "text-align:center;padding:1rem;";
    more.innerHTML = '<button onclick="admLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button>';
    c.appendChild(more);
  }
}'''
if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

cd /home/khedmotcenter/htdocs/khedmotcenter.com
grep -n "async function admLoadMorePosts" index.html
wget -O /tmp/p2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_usertab2.py && python3 /tmp/p2.py
python3 << 'PYEOF'
content = open('index.html', 'r').read()
old = '''async function admLoadMorePosts() { const lm = document.getElementById("admLoadMore"); if (lm) lm.remove(); const btn = document.querySelector("#admLoadMore button"); if (btn) btn.textContent = "লোড হচ্ছে..."; const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10}); if (!res.success || !res.posts.length) return; const c = document.getElementById("postsContainer"); res.posts.forEach(p => { const div = document.createElement("div"); div.className = "post-item"; div.id = "postItem"+p.id; div.innerHTML = "<div class=adm-post-text>"+p.content+"</div>"; c.appendChild(div); }); window._admOffset = (window._admOffset || 10) + res.posts.length; if (res.posts.length >= 20) { const more = document.createElement("div"); more.id = "admLoadMore"; more.style = "text-align:center;padding:1rem;"; more.innerHTML = "<button onclick=admLoadMorePosts() style=background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;>খেদমতের খবর আরো দেখুন</button>"; c.appendChild(more); } }'''
new = '''async function admLoadMorePosts() {
  const lm = document.getElementById("admLoadMore");
  const btn = lm ? lm.querySelector("button") : null;
  if (btn) btn.textContent = "লোড হচ্ছে...";
  const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10});
  if (lm) lm.remove();
  if (!res.success || !res.posts.length) return;
  const c = document.getElementById("postsContainer");
  const catMap = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  res.posts.forEach(p => {
    const div = document.createElement("div");
    div.className = "post-item";
    div.id = "postItem"+p.id;
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c2=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap[c2]||c2}</span>`).join("");
    div.innerHTML = `<div class="post-item-date">${admFmtDt(p.created)}<div class="post-item-actions"><button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button><button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button></div></div><div class="post-item-body">${catTags?catTags+"<br>":""}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}<div class="adm-post-text">${admEsc(p.content)}</div></div>`;
    c.appendChild(div);
  });
  window._admOffset = (window._admOffset || 10) + res.posts.length;
  if (res.posts.length >= 20) {
    const more = document.createElement("div");
    more.id = "admLoadMore";
    more.style = "text-align:center;padding:1rem;";
    more.innerHTML = '<button onclick="admLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button>';
    c.appendChild(more);
  }
}'''
if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done")
else:
    print("NOT FOUND")
PYEOF

sed -i '1773s/.*/async function admLoadMorePosts() {\n  const lm = document.getElementById("admLoadMore");\n  const btn = lm ? lm.querySelector("button") : null;\n  if (btn) btn.textContent = "লোড হচ্ছে...";\n  const res = await api("admin_get_posts", {limit: 20, offset: window._admOffset || 10});\n  if (lm) lm.remove();\n  if (!res.success || !res.posts.length) return;\n  const c = document.getElementById("postsContainer");\n  res.posts.forEach(p => {\n    const div = document.createElement("div");\n    div.className = "post-item";\n    div.id = "postItem"+p.id;\n    div.innerHTML = `<div class="post-item-date">${admFmtDt(p.created)}<div class="post-item-actions"><button class="btn btn-warning btn-sm" onclick="openEditPost(${p.id})">edit<\/button><button class="btn btn-danger btn-sm" onclick="deletePost(${p.id})">del<\/button><\/div><\/div><div class="post-item-body"><div class="adm-post-text">${admEsc(p.content)}<\/div><\/div>`;\n    c.appendChild(div);\n  });\n  window._admOffset = (window._admOffset || 10) + res.posts.length;\n  if (res.posts.length >= 20) {\n    const more = document.createElement("div");\n    more.id = "admLoadMore";\n    more.style = "text-align:center;padding:1rem;";\n    more.innerHTML = "<button onclick=\\"admLoadMorePosts()\\" style=\\"background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;\\">খেদমতের খবর আরো দেখুন<\/button>";\n    c.appendChild(more);\n  }\n}/' index.html
echo "Done"
cd /home/khedmotcenter/htdocs/khedmotcenter.com
grep -n "async function admLoadMorePosts" index.html
ls -la favicon*
cd /home/khedmotcenter/htdocs/khedmotcenter.com && ls -la favicon*
grep -n "favicon" index.html
cd /home/khedmotcenter/htdocs/khedmotcenter.com && ls -la favicon*
tar -czf /root/khedmotcenter_backup_$(date +%Y%m%d_%H%M).tar.gz /home/khedmotcenter/htdocs/khedmotcenter.com/ && ls -lh /root/khedmotcenter_backup_*.tar.gz | tail -1
curl -I https://khedmotcenter.com/favicon.png 2>/dev/null | head -5
tar -czf /root/khedmotcenter_final_backup_$(date +%Y%m%d_%H%M).tar.gz . && ls -lh /root/khedmotcenter*backup*.tar.gz | tail -3
grep -n "খেদমতের খবর আরও\|pagination\|load more" index.html | head -10
grep -n "আরও\|আরও দেখুন\|পরবর্তী" index.html | head -10
grep -n "pageNews\|newsContainer" index.html | head -5
sed -n '953,986p' index.html
grep -n "newsPostsWrap\|loadNews\|loadMore" index.html | head -10
sed -n '1494,1530p' index.html
grep -n "get_public_news\|get_user_dashboard\|admin_get_posts" api.php | head -5
grep -n "get_filter_counts" api.php
sed -n '298,330p' api.php
sed -n '241,263p' api.php
# API endpoints check
curl -s http://localhost:8080/api.php -X POST -H "Content-Type: application/json" -d '{"action":"get_filter_counts"}' | head -100
curl -s http://localhost:8080/api.php -X POST -H "Content-Type: application/json" -d '{"action":"track_visitor","visitor_id":"test_check","source":"direct"}'
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT COUNT(*) as total_posts FROM posts; SELECT COUNT(*) as total_visitors FROM visitor_logs;"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "
SELECT 
  (SELECT COUNT(*) FROM posts WHERE status='active' AND created>=(UNIX_TIMESTAMP()-24*3600)*1000) as '24h_total',
  (SELECT COUNT(*) FROM posts WHERE status='active' AND created>=(UNIX_TIMESTAMP()-48*3600)*1000) as '48h_total',
  (SELECT COUNT(*) FROM posts WHERE status='active' AND created>=(UNIX_TIMESTAMP()-72*3600)*1000) as '72h_total';
"
git log --oneline | head -10
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT SUM(CASE WHEN (category='mosque-jobs' OR cats LIKE '%mosque-jobs%') THEN 1 ELSE 0 END) as mosque_jobs, SUM(CASE WHEN (category='male-madrasa-jobs' OR cats LIKE '%male-madrasa-jobs%') THEN 1 ELSE 0 END) as male_madrasa, SUM(CASE WHEN (category='female-madrasa-jobs' OR cats LIKE '%female-madrasa-jobs%') THEN 1 ELSE 0 END) as female_madrasa FROM posts WHERE status='active';"
curl -s http://localhost:8080/api.php -X POST -H "Content-Type: application/json" -d '{"action":"get_filter_counts"}' | python3 -m json.tool | grep -A 20 "cat"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "
SELECT 
  (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='mosque-jobs' OR cats LIKE '%mosque-jobs%') AND created>=(UNIX_TIMESTAMP()-24*3600)*1000) as '24h_mosque',
  (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='male-madrasa-jobs' OR cats LIKE '%male-madrasa-jobs%') AND created>=(UNIX_TIMESTAMP()-24*3600)*1000) as '24h_male',
  (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='female-madrasa-jobs' OR cats LIKE '%female-madrasa-jobs%') AND created>=(UNIX_TIMESTAMP()-24*3600)*1000) as '24h_female';
"
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='mosque-jobs' OR cats LIKE '%mosque-jobs%') AND created>=(UNIX_TIMESTAMP()-48*3600)*1000) as '48h_mosque', (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='male-madrasa-jobs' OR cats LIKE '%male-madrasa-jobs%') AND created>=(UNIX_TIMESTAMP()-48*3600)*1000) as '48h_male', (SELECT COUNT(*) FROM posts WHERE status='active' AND (category='female-madrasa-jobs' OR cats LIKE '%female-madrasa-jobs%') AND created>=(UNIX_TIMESTAMP()-48*3600)*1000) as '48h_female';"
grep -n "category.*filter\|loadNews.*category" index.html | head -10
grep -n "onclick.*category\|mosque-jobs\|male-madrasa-jobs" index.html | grep -i "button\|onclick" | head -15
grep -n "function filterByCat\|async function filterByCat" index.html
sed -n '1430,1460p' index.html
grep -n "function applyFilter\|async function applyFilter" index.html
sed -n '1395,1428p' index.html
sed -n '241,262p' api.php
grep -n "favicon\|logo.png" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -5
ls -lh /home/khedmotcenter/htdocs/khedmotcenter.com/logo.png
grep -n "loadNews\|loadMore\|আরো দেখুন\|offset\|limit" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
wget -O /tmp/pis.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_infinitescroll.py && python3 /tmp/pis.py
git add index.html && git commit -m "infinite scroll news" && git push origin main
git pull origin main --rebase && git push origin main
wget -O /tmp/pbs.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_both_scroll.py && python3 /tmp/pbs.py
git add index.html && git commit -m "infinite scroll user and admin dashboard" && git push origin main
git pull origin main --rebase && git push origin main
grep -n "filterByCat\|male-madrasa\|female-madrasa\|mosque" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | grep -v "catMap\|নিয়োগ\|jobs\|span\|background" | head -20
sed -n '1430,1460p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "applyFilter\|applyFilterUD\|get_public_news.*cat\|category.*filter" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
sed -n '1395,1415p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
wget -O /tmp/pcf.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_catfilter.py && python3 /tmp/pcf.py
git add index.html && git commit -m "fix category filter" && git push origin main
git pull origin main --rebase && git push origin main
git log --oneline | head -5
git revert faf6a14 --no-edit && git push origin main
mysql -u khedmotuser -p'khedmot@722' khedmotdb -e "SELECT DISTINCT category FROM posts LIMIT 20;"
grep -n "category\|WHERE\|AND cat" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -15
sed -i 's/if (\$cat) { \$where .= " AND (category=? OR cats LIKE ?)"; \$params\[\] = \$cat; \$params\[\] = '"'"'%'"'"'.\$cat.'"'"'%'"'"'; \$types .= '"'"'ss'"'"'; }/if (\$cat) { \$where .= " AND category=?"; \$params[] = \$cat; \$types .= '"'"'s'"'"'; }/' /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
grep -n "AND category\|cats LIKE" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -5
sed -n '352,356p' /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
wget -O /tmp/pac.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_api_cat.py && python3 /tmp/pac.py
git add api.php && git commit -m "fix category filter api" && git push origin main
git pull origin main --rebase && git push origin main
grep -n "category\|ক্যাটাগরি\|mosque-jobs\|categories" /home/khedmotcenter/htdocs/khedmotcenter.com/api.php | head -30
grep -n "position\|address.*input\|postCategory\|category.*select" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -20
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git log --oneline | head -10
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git reset --hard c40ade5 && git clean -fd && echo "done"
git log --oneline | head -30
grep -n "postCategory\|admin_create_post.*category" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
grep -n "filterByCat\|function filterByCat" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
ls -la /root/*.php /root/*.html 2>/dev/null
find /root -name "*.html" -newer /root/index_backup_20260620.html 2>/dev/null
find /root -name "*.php" -newer /root/api_backup.php 2>/dev/null
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git log --oneline --since="2026-06-21"
find / -name "*.html" -newer /root/index_backup_20260620.html 2>/dev/null | grep -v "/proc\|/sys"
find / -name "*.php" -mtime -1 2>/dev/null | grep -v "/proc\|/sys"
cp /root/index.html /home/khedmotcenter/htdocs/khedmotcenter.com/index.html && echo "done"
cp /home/khedmotcenter/htdocs/khedmotcenter.com/index.html /tmp/before_restore_check.html 2>/dev/null
git diff --stat HEAD -- index.html 2>/dev/null
git checkout HEAD -- index.html && echo "done"
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git checkout HEAD -- index.html && echo "done"
ls -lh /root/backup_*.tar.gz
find / -name "*.tar.gz" -newer /root/backup_before_visitor.tar.gz 2>/dev/null
tar -tzf /root/khedmotcenter_final_backup_20260620_2327.tar.gz | head -20
tar -xzf /root/khedmotcenter_final_backup_20260620_2327.tar.gz -C /home/khedmotcenter/htdocs/khedmotcenter.com/
php -l /home/khedmotcenter/htdocs/khedmotcenter.com/api.php
ls /home/khedmotcenter/htdocs/khedmotcenter.com/
grep -n "favicon\|filterByCat\|setUserTab\|returning\|logo.png" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
sed -n '1140,1160p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "async function loadNews\|get_public_news\|newsLoaded\|newsPostsWrap" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
sed -n '1494,1540p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
sed -n '1430,1495p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
root@srv1737072:~# sed -n '1430,1495p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
function filterByCat(cat, containerId) {
}
async function filterByHours(hours, containerId) {
}
async function loadNews() {
root@srv1737072:~#
sed -n '1395,1430p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
cp /home/khedmotcenter/htdocs/khedmotcenter.com/index.html ~/index_before_scroll.html
wget -O /tmp/ps.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_scroll.py && python3 /tmp/ps.py
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "infinite scroll" && git push origin main
git pull origin main --rebase && git push origin main
rm -f patch_api_cat.py patch_both_scroll.py patch_catfilter.py patch_infinitescroll.py && git pull origin main --rebase && git push origin main
git rebase --abort && git push origin main --force
grep -n "applyFilterUD\|udMainContent\|admLoadPosts\|ud-post-card" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
sed -n '1655,1700p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
grep -n "applyFilterUD\|udMainContent\|admLoadPosts\|ud-post-card" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -15
sed -n '1655,1700p' /home/khedmotcenter/htdocs/khedmotcenter.com/index.html
wget -O /tmp/puds.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_ud_scroll.py && python3 /tmp/puds.py
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "user dashboard infinite scroll" && git push origin main
git push origin main --force
grep -n "newsLoadMore\|আরো দেখুন\|loadMoreNews" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
wget -O /tmp/pral.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_remove_all_loadmore.py && python3 /tmp/pral.py
git add index.html && git commit -m "remove load more buttons" && git push origin main --force
ls ~/index_*.html
git log --oneline | head -10
git checkout 5178d25 -- index.html
grep -n "scroll_sentinel\|IntersectionObserver" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -5
git pull origin main
grep -n "loadMoreNews\|newsLoadMore\|আরো দেখুন\|খেদমতের খবর আরো" /home/khedmotcenter/htdocs/khedmotcenter.com/index.html | head -10
wget -O /tmp/ps2.py https://raw.githubusercontent.com/daruttaqwa722/Masjid-Madrashar-Khedmot-Senter-/main/patch_scroll2.py && python3 /tmp/ps2.py
cd /home/khedmotcenter/htdocs/khedmotcenter.com && git add index.html && git commit -m "infinite scroll news and filter" && git push origin main
git pull origin main --rebase && git push origin main
cd /home/khedmotcenter/htdocs/khedmotcenter.com
git fetch origin
git log --oneline origin/main -5
cp index.html index.html.backup-$(date +%Y%m%d-%H%M).bak
cp api.php api.php.backup-$(date +%Y%m%d-%H%M).bak
echo "Backup done"
git show 164e372 --stat
git show 164e372 -- index.html | head -150
grep -n "udLoadMore\|udLoadMorePosts\|_udOffset\|scroll_sentinel" index.html | head -15
sed -n '1628,1665p' index.html
sed -n '1628,1665p' index.htmlpython3 << 'PYEOF'
content = open('index.html', 'r').read()

# পুরনো button + udLoadMorePosts function খুঁজি
old = """  if (posts.length >= 10) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id="udLoadMore" style="text-align:center;padding:1rem;"><button onclick="udLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button></div>');
  }
  load72hBar('udMainContent');
}
async function udLoadMorePosts() {"""

if old in content:
    print("FOUND")
else:
    print("NOT FOUND - checking...")
    idx = content.find("udLoadMorePosts")
    print(repr(content[idx-200:idx+50]))
PYEOF

cd /home/khedmotcenter/htdocs/khedmotcenter.com
python3 << 'PYEOF'
content = open('index.html', 'r').read()
idx = content.find('udLoadMorePosts')
print(repr(content[idx-100:idx+50]))
PYEOF

grep -n "async function udLoadMorePosts\|^}" index.html | awk -F: '$1>1635 && $1<1680'
oot@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# grep -n "async function udLoadMorePosts\|^}" index.html | awk -F: '$1>1635 && $1<1680'
1638:}
1639:async function udLoadMorePosts() {
1658:}
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
grep -n "async function udLoadMorePosts" index.html
sed -n '1639,1670p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

# ১. loadUserDashboard এ button সরিয়ে infinite scroll দিই
old1 = """  window._udOffset = posts.length;
  if (posts.length >= 10) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id="udLoadMore" style="text-align:center;padding:1rem;"><button onclick="udLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button></div>');
  }
  load72hBar('udMainContent');
}"""

new1 = """  window._udOffset = posts.length;
  load72hBar('udMainContent');
  // Infinite scroll for user dashboard
  window._udScrollState = {offset: posts.length, done: posts.length < 10, loading: false};
  const oldUdSen = document.getElementById('scroll_sentinel_udMainContent');
  if (oldUdSen) oldUdSen.remove();
  if (posts.length >= 10) {
    const udSen = document.createElement('div');
    udSen.id = 'scroll_sentinel_udMainContent';
    udSen.style.height = '10px';
    document.getElementById('udMainContent').appendChild(udSen);
    const udObs = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st = window._udScrollState;
      if (!st || st.done || st.loading) return;
      st.loading = true;
      const r2 = await api('get_user_dashboard', {mobile: window._userMobile || '', limit: 10, offset: st.offset});
      if (r2.success && r2.posts.length) {
        const catMap2 = {'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'};
        const html2 = r2.posts.map(p => {
          const ct = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap2[c]||c}</span>`).join('');
          return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${ct?`<div style="margin:0.3rem 0.8rem;">${ct}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
        }).join('');
        udSen.insertAdjacentHTML('beforebegin', html2);
        st.offset += r2.posts.length;
        if (r2.posts.length < 10) { st.done = true; udObs.disconnect(); udSen.remove(); }
      } else { st.done = true; udObs.disconnect(); udSen.remove(); }
      st.loading = false;
    }, {rootMargin: '300px'});
    udObs.observe(udSen);
  }
}"""

if old1 in content:
    content = content.replace(old1, new1)
    print("Step 1 done")
else:
    print("Step 1 NOT FOUND")

open('index.html', 'w').write(content)
print("Saved")
PYEOF

cd /home/khedmotcenter/htdocs/khedmotcenter.com && python3 << 'PYEOF'
content = open('index.html', 'r').read()

old1 = """  window._udOffset = posts.length;
  if (posts.length >= 10) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id="udLoadMore" style="text-align:center;padding:1rem;"><button onclick="udLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button></div>');
  }
  load72hBar('udMainContent');
}"""

if old1 in content:
    print("Found!")
else:
    print("NOT FOUND - searching for partial match...")
    if "window._udOffset = posts.length" in content:
        print("Found _udOffset, searching nearby...")
        idx = content.find("window._udOffset = posts.length")
        print(content[idx:idx+300])
PYEOF

python3 << 'PYEOF'
content = open('index.html', 'r').read()

old1 = """  window._udOffset = posts.length;
  if (posts.length >= 10) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id="udLoadMore" style="text-align:center;padding:1rem;"><button onclick="udLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button></div>');
  }
  load72hBar('udMainContent');
}"""

new1 = """  window._udOffset = posts.length;
  load72hBar('udMainContent');
  window._udScrollState = {offset: posts.length, done: posts.length < 10, loading: false};
  const oldUdSen = document.getElementById('scroll_sentinel_udMainContent');
  if (oldUdSen) oldUdSen.remove();
  if (posts.length >= 10) {
    const udSen = document.createElement('div');
    udSen.id = 'scroll_sentinel_udMainContent';
    udSen.style.height = '10px';
    document.getElementById('udMainContent').appendChild(udSen);
    const udObs = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st = window._udScrollState;
      if (!st || st.done || st.loading) return;
      st.loading = true;
      const r2 = await api('get_user_dashboard', {mobile: window._userMobile || '', limit: 10, offset: st.offset});
      if (r2.success && r2.posts.length) {
        const catMap2 = {'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ'};
        const html2 = r2.posts.map(p => {
          const ct = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap2[c]||c}</span>`).join('');
          return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${ct?`<div style="margin:0.3rem 0.8rem;">${ct}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
        }).join('');
        udSen.insertAdjacentHTML('beforebegin', html2);
        st.offset += r2.posts.length;
        if (r2.posts.length < 10) { st.done = true; udObs.disconnect(); udSen.remove(); }
      } else { st.done = true; udObs.disconnect(); udSen.remove(); }
      st.loading = false;
    }, {rootMargin: '300px'});
    udObs.observe(udSen);
  }
}"""

if old1 in content:
    content = content.replace(old1, new1)
    open('index.html', 'w').write(content)
    print("Done! Infinite scroll added to User Dashboard")
else:
    print("NOT FOUND")
PYEOF

grep -n "function udLoadMorePosts\|async function udLoadMorePosts" index.html
sed -n '1663,1700p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

old = """async function udLoadMorePosts() {
  const btn = document.querySelector('#udLoadMore button');
  if (btn) btn.textContent = 'লোড হচ্ছে...';
  const res = await api('get_user_dashboard', {mobile: window._userMobile || '', limit: 20, offset: window._udOffset || 10});
  if (!res.success || !res.posts.length) { const lm = document.getElementById('udLoadMore'); if (lm) lm.remove(); return; }
  const lm = document.getElementById('udLoadMore');
  if (lm) lm.remove();
  const catMap = {'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'};
  const html = res.posts.map(p => {
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap[c]||c}</span>`).join('');
    return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${catTags?`<div style="margin:0.3rem 0.8rem;">${catTags}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
  }).join('');
  document.getElementById('udMainContent').insertAdjacentHTML('beforeend', html);
  window._udOffset = (window._udOffset || 10) + res.posts.length;
  if (res.posts.length >= 20) {
    document.getElementById('udMainContent').insertAdjacentHTML('beforeend',
      '<div id="udLoadMore" style="text-align:center;padding:1rem;"><button onclick="udLoadMorePosts()" style="background:#05057D;color:#FFD700;border:none;border-radius:20px;padding:10px 30px;font-size:0.95rem;font-weight:700;cursor:pointer;font-family:inherit;">খেদমতের খবর আরো দেখুন</button></div>');
  }
}
"""

if old in content:
    content = content.replace(old, '')
    open('index.html', 'w').write(content)
    print("udLoadMorePosts() function deleted!")
else:
    print("NOT FOUND")
PYEOF

php -l index.html
git add index.html && git commit -m "Add Infinite Scroll to User Dashboard - remove udLoadMorePosts button" && git push origin main
grep -n "async function applyFilterUD" index.html
sed -n '1663,1720p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

old = """async function applyFilterUD(containerId) {
  if (!window._activeFilter) window._activeFilter = {};
  if (!window._activeFilter[containerId]) window._activeFilter[containerId] = {hours: 72, cat: ""};
  const hours = window._activeFilter[containerId].hours || 72;
  const cat = window._activeFilter[containerId].cat || "";
  const params = {limit: 200, offset: 0, hours: hours, mobile: window._userMobile || ""};
  if (cat) params.category = cat;
  const res = await api("get_user_dashboard", params);
  if (!res.success) return;
  const posts = res.posts;
  const catMap = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  const wrap = document.getElementById(containerId);
  if (!wrap) return;
  wrap.querySelectorAll(".ud-post-card, .post-card, .empty-state").forEach(c => c.remove());
  const catMap2 = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  const html = posts.length ? posts.map(p => {
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap2[c]||c}</span>`).join('');
    return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${catTags?`<div style="margin:0.3rem 0.8rem;">${catTags}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
  }).join('') : `<div class="empty-state"><div class="empty-icon">📭</div><div class="empty-text">এই ফিল্টারে কোনো পোস্ট নেই।</div></div>`;
  wrap.insertAdjacentHTML("beforeend", html);
}"""

new = """async function applyFilterUD(containerId) {
  if (!window._activeFilter) window._activeFilter = {};
  if (!window._activeFilter[containerId]) window._activeFilter[containerId] = {hours: 72, cat: ""};
  const hours = window._activeFilter[containerId].hours || 72;
  const cat = window._activeFilter[containerId].cat || "";
  const params = {limit: 10, offset: 0, hours: hours, mobile: window._userMobile || ""};
  if (cat) params.category = cat;
  const res = await api("get_user_dashboard", params);
  if (!res.success) return;
  const posts = res.posts;
  const catMap = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  const wrap = document.getElementById(containerId);
  if (!wrap) return;
  wrap.querySelectorAll(".ud-post-card, .post-card, .empty-state, #scroll_sentinel_udMainContent").forEach(c => c.remove());
  const catMap2 = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ","mosque":"মসজিদের নিয়োগ","male-madrasa":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa":"মহিলা মাদ্রাসার নিয়োগ"};
  const html = posts.length ? posts.map(p => {
    const cats = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean);
    const catTags = cats.map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap2[c]||c}</span>`).join('');
    return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${catTags?`<div style="margin:0.3rem 0.8rem;">${catTags}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
  }).join('') : `<div class="empty-state"><div class="empty-icon">📭</div><div class="empty-text">এই ফিল্টারে কোনো পোস্ট নেই।</div></div>`;
  wrap.insertAdjacentHTML("beforeend", html);
  window._udFilterState = {hours: hours, cat: cat, offset: posts.length, done: posts.length < 10, loading: false};
  if (posts.length >= 10) {
    const udSen = document.createElement('div');
    udSen.id = 'scroll_sentinel_udMainContent';
    udSen.style.height = '10px';
    wrap.appendChild(udSen);
    const udObs = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st = window._udFilterState;
      if (!st || st.done || st.loading) return;
      st.loading = true;
      const p2 = {limit: 10, offset: st.offset, hours: st.hours, mobile: window._userMobile || ""};
      if (st.cat) p2.category = st.cat;
      const r2 = await api("get_user_dashboard", p2);
      if (r2.success && r2.posts.length) {
        const cm = {"mosque-jobs":"মসজিদের নিয়োগ","male-madrasa-jobs":"পুরুষ মাদ্রাসার নিয়োগ","female-madrasa-jobs":"মহিলা মাদ্রাসার নিয়োগ"};
        const h2 = r2.posts.map(p => {
          const ct = (Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${cm[c]||c}</span>`).join('');
          return `<div class="ud-post-card"><div class="ud-post-card-date">${formatDatetimeUD(p.created)}</div>${ct?`<div style="margin:0.3rem 0.8rem;">${ct}</div>`:''}<div class="ud-post-card-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${escHtml(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${escHtml(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${escHtml(p.title)}</div>`:''}<div class="post-text">${escHtml(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`;
        }).join('');
        udSen.insertAdjacentHTML('beforebegin', h2);
        st.offset += r2.posts.length;
        if (r2.posts.length < 10) { st.done = true; udObs.disconnect(); udSen.remove(); }
      } else { st.done = true; udObs.disconnect(); udSen.remove(); }
      st.loading = false;
    }, {rootMargin: '300px'});
    udObs.observe(udSen);
  }
}"""

if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done! applyFilterUD updated with infinite scroll")
else:
    print("NOT FOUND")
PYEOF

php -l index.html
git add index.html && git commit -m "Add Infinite Scroll to all User Dashboard filters (24h, 48h, 72h, categories)" && git push origin main
tar -czf /root/khedmotcenter_final_$(date +%Y%m%d_%H%M).tar.gz . && echo "Done!"
ls -lh /root/khedmotcenter_final_*.tar.gz
grep -n "loadAdminPosts\|function.*admin.*posts" index.html | head -10
cd /home/khedmotcenter/htdocs/khedmotcenter.com && grep -n "admin.*dashboard\|pageAdminDashboard" index.html | head -5
grep -n "admLoadPosts\|admin_get_posts\|postsContainer" index.html | grep -i "function\|onclick" | head -10
sed -n '1840,1862p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

# admLoadPosts function শুরু থেকে শেষ পর্যন্ত
old_start = "async function admLoadPosts() {"
old_end = "  }\n}"

start_idx = content.find(old_start)
end_idx = content.find(old_end, start_idx) + len(old_end)

if start_idx != -1 and end_idx != -1:
    old_func = content[start_idx:end_idx]
    
    new_func = """async function admLoadPosts() {
  const res = await api('admin_get_posts', {limit: 10, offset: 0});
  load72hBar('postsContainer');
  const c = document.getElementById('postsContainer');
  if (!res.success || !res.posts.length) { c.innerHTML = '<div class="adm-empty">কোনো পোস্ট নেই।</div>'; return; }
  c.innerHTML = res.posts.map(p => `
    <div class="post-item" id="postItem${p.id}">
      <div class="post-item-date">${admFmtDt(p.created)}
        <div class="post-item-actions">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \\`${admEsc(p.content).replace(/\\`/g,"'")}\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-item-body">${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')?((Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')+`<br>`):''}${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${admEsc(p.position)}</div>`:""}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${admEsc(p.address)}</div>`:""}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}<div class="adm-post-text">${admEsc(p.content)}</div>${p.image_path?`<img class="adm-post-img" src="${p.image_path}" loading="lazy">`:''}  </div>
    </div>`).join('');
  window._admOffset = res.posts.length;
  window._admScrollState = {offset: res.posts.length, done: res.posts.length < 10, loading: false};
  if (res.posts.length >= 10) {
    const admSen = document.createElement('div');
    admSen.id = 'scroll_sentinel_postsContainer';
    admSen.style.height = '10px';
    c.appendChild(admSen);
    const admObs = new IntersectionObserver(async (entries) => {
      if (!entries[0].isIntersecting) return;
      const st = window._admScrollState;
      if (!st || st.done || st.loading) return;
      st.loading = true;
      const r2 = await api('admin_get_posts', {limit: 10, offset: st.offset});
      if (r2.success && r2.posts.length) {
        const h2 = r2.posts.map(p => `<div class="post-item" id="postItem${p.id}"><div class="post-item-date">${admFmtDt(p.created)}<div class="post-item-actions"><button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \\`${admEsc(p.content).replace(/\\`/g,"'")}\`)">✏️</button><button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button></div></div><div class="post-item-body">${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')}${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${admEsc(p.position)}</div>`:""}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${admEsc(p.address)}</div>`:""}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}<div class="adm-post-text">${admEsc(p.content)}</div>${p.image_path?`<img class="adm-post-img" src="${p.image_path}" loading="lazy">`:''}</div></div>`).join('');
        admSen.insertAdjacentHTML('beforebegin', h2);
        st.offset += r2.posts.length;
        if (r2.posts.length < 10) { st.done = true; admObs.disconnect(); admSen.remove(); }
      } else { st.done = true; admObs.disconnect(); admSen.remove(); }
      st.loading = false;
    }, {rootMargin: '300px'});
    admObs.observe(admSen);
  }
}"""
    
    content = content.replace(old_func, new_func)
    open('index.html', 'w').write(content)
    print("Admin infinite scroll added!")
else:
    print("Function not found")
PYEOF

grep -n "async function admLoadMorePosts" index.html
php -l index.html
git add index.html && git commit -m "Add Infinite Scroll to Admin Dashboard posts" && git push origin main
tar -czf /root/khedmotcenter_complete_$(date +%Y%m%d_%H%M).tar.gz . && ls -lh /root/khedmotcenter_complete_*.tar.gz
python3 << 'PYEOF'
content = open('index.html', 'r').read()

# Admin posts HTML structure change - .post-item থেকে .post-card এ
old = """c.innerHTML = res.posts.map(p => `
    <div class="post-item" id="postItem${p.id}">
      <div class="post-item-date">${admFmtDt(p.created)}
        <div class="post-item-actions">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-item-body">${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')?((Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')+`<br>`):''}${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${admEsc(p.position)}</div>`:""}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${admEsc(p.address)}</div>`:""}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}<div class="adm-post-text">${admEsc(p.content)}</div>${p.image_path?`<img class="adm-post-img" src="${p.image_path}" loading="lazy">`:''}  </div>
    </div>`).join('')"""

new = """c.innerHTML = res.posts.map(p => `<div class="post-card"><div class="post-date">${admFmtDt(p.created)}<div style="float:right;"><button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \\\`${admEsc(p.content).replace(/\\\`/g,"'")}\\\`)">✏️</button> <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button></div></div>${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')?`<div style="padding:0.3rem 0.85rem;">${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(c=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${({'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'})[c]||c}</span>`).join('')}</div>`:''}><div class="post-body">${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${admEsc(p.position)}</div>`:''}${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${admEsc(p.address)}</div>`:''}${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:6px;">${admEsc(p.title)}</div>`:''}}<div class="post-text">${admEsc(p.content)}</div>${p.image_path?`<img class="post-img" src="${p.image_path}" alt="ছবি" loading="lazy">`:''}</div></div>`).join('')"""

if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done! Admin Dashboard now uses post-card style")
else:
    print("NOT FOUND")
PYEOF

php -l index.html
git add index.html && git commit -m "Admin Dashboard UI - Change to post-card style (bigger display like News Feed)" && git push origin main
git revert HEAD --no-edit && git push origin main
grep -n "\.post-item\|\.adm-post" index.html | head -20
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com# grep -n "\.post-item\|\.adm-post" index.html | head -20
473:  .post-item { background: rgba(5,5,125,0.02); border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 0.75rem; }
474:  .post-item:last-child { margin-bottom: 0; }
475:  .post-item-date { background: rgba(5,5,125,0.06); padding: 0.4rem 0.8rem; font-size: 0.68rem; color: var(--text-sub); font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
476:  .post-item-actions { display: flex; gap: 5px; }
477:  .post-item-body { padding: 0.75rem 0.8rem; }
478:  .adm-post-text { font-size: 0.88rem; line-height: 1.8; color: var(--text); white-space: pre-wrap; }
479:  .adm-post-img { width: 100%; border-radius: 10px; margin-top: 0.5rem; max-height: 220px; object-fit: cover; }
1405:  const existingCards = wrap.querySelectorAll('.post-card, .post-item, .ud-post-card');
1424:  wrap.querySelectorAll('.post-card, .post-item, .empty').forEach(c => c.remove());
root@srv1737072:/home/khedmotcenter/htdocs/khedmotcenter.com#
sed -n '400,450p' index.html | grep "post-card\|post-body\|post-text"
sed -n '360,400p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

old = """  .post-item { background: rgba(5,5,125,0.02); border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 0.75rem; }
  .post-item:last-child { margin-bottom: 0; }
  .post-item-date { background: rgba(5,5,125,0.06); padding: 0.4rem 0.8rem; font-size: 0.68rem; color: var(--text-sub); font-weight: 600; display: flex; justify-content: space-between; align-items: center; }
  .post-item-actions { display: flex; gap: 5px; }
  .post-item-body { padding: 0.75rem 0.8rem; }
  .adm-post-text { font-size: 0.88rem; line-height: 1.8; color: var(--text); white-space: pre-wrap; }
  .adm-post-img { width: 100%; border-radius: 10px; margin-top: 0.5rem; max-height: 220px; object-fit: cover; }"""

new = """  .post-item { background: var(--card-bg); border: 1.5px solid var(--border); border-radius: 14px; overflow: hidden; margin-bottom: 1rem; box-shadow: var(--shadow-sm); }
  .post-item:last-child { margin-bottom: 0; }
  .post-item-date { background: rgba(5,5,125,0.06); padding: 0.5rem 0.9rem; font-size: 0.75rem; color: var(--text-sub); font-weight: 600; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border); }
  .post-item-actions { display: flex; gap: 8px; }
  .post-item-body { padding: 1rem; }
  .adm-post-text { font-size: 1.05rem; line-height: 2; color: var(--text); white-space: pre-wrap; font-family: "Noto Serif Bengali", serif; }
  .adm-post-img { width: 100%; border-radius: 12px; margin-top: 0.8rem; max-height: 320px; object-fit: cover; }"""

if old in content:
    content = content.replace(old, new)
    open('index.html', 'w').write(content)
    print("Done! Admin Dashboard CSS updated to match News Feed style")
else:
    print("NOT FOUND")
PYEOF

php -l index.html
git add index.html && git commit -m "Admin Dashboard CSS - Improved styling to match News Feed (bigger, better spacing)" && git push origin main
sed -n '1840,1865p' index.html
grep -n "class=\"post-card\"" index.html | head -3
python3 << 'PYEOF'
content = open('index.html', 'r').read()

# Admin posts HTML structure change করছি - post-item কে post-card এ convert
old_adm = '''c.innerHTML = res.posts.map(p => `
    <div class="post-item" id="postItem${p.id}">
      <div class="post-item-date">${admFmtDt(p.created)}
        <div class="post-item-actions">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-item-body">'''

new_adm = '''c.innerHTML = res.posts.map(p => `
    <div class="post-card" id="postItem${p.id}">
      <div class="post-date">${admFmtDt(p.created)}</div>
      <div style="padding:0.3rem 0.85rem;display:flex;gap:0.5rem;">
        <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️ এডিট</button>
        <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️ ডিলিট</button>
      </div>
      <div class="post-body">'''

if old_adm in content:
    content = content.replace(old_adm, new_adm)
    open('index.html', 'w').write(content)
    print("Step 1: Admin posts structure updated!")
else:
    print("Step 1 NOT FOUND")
PYEOF

cd /home/khedmotcenter/htdocs/khedmotcenter.com && python3 << 'PYEOF'
content = open('index.html', 'r').read()

old_close = '''</div>
    </div>`).join('');'''

new_close = '''</div>
    </div>`).join('');'''

# এখন post-item-body closing tag কে post-body তে change করছি
old_body_close = '''</div>
      </div>
    </div>`).join('');'''

new_body_close = '''</div>
    </div>`).join('');'''

if old_body_close in content:
    content = content.replace(old_body_close, new_body_close)
    open('index.html', 'w').write(content)
    print("Closing tags fixed!")
else:
    print("Already fixed or not found")
PYEOF

php -l index.html
git add index.html && git commit -m "Update Admin Dashboard posts styling to post-card (News Page style)" && git push origin main
grep -n "class=\"post-card\"" index.html | head -3
sed -n '1840,1860p' index.html
python3 << 'PYEOF'
content = open('index.html', 'r').read()

# Admin posts HTML structure change করব
old_post_item = '''<div class="post-item" id="postItem${p.id}">
      <div class="post-item-date">${admFmtDt(p.created)}
        <div class="post-item-actions">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-item-body">'''

new_post_item = '''<div class="post-card" id="postItem${p.id}">
      <div class="post-date">${admFmtDt(p.created)}
        <div style="display:flex;gap:8px;">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \`${admEsc(p.content).replace(/\`/g,"'")}\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-body">'''

if old_post_item in content:
    content = content.replace(old_post_item, new_post_item)
    # এছাড়াও post-item-body কে post-body এ change করব
    content = content.replace('class="post-item-body"', 'class="post-body"')
    content = content.replace('class="adm-post-text"', 'class="post-text"')
    content = content.replace('class="adm-post-img"', 'class="post-img"')
    open('index.html', 'w').write(content)
    print("Admin posts styling updated to match News Page!")
else:
    print("NOT FOUND")
PYEOF

php -l index.html
git add index.html && git commit -m "Admin Dashboard posts styling - match News Page design" && git push origin main
ব্যাকআপটা /root/khedmotcenter_complete_20260622_0123.tar.gz এ আছে। এটা June 22, 2026 এর 01:23 মিনিটে নেওয়া। Size: 21MB। এটা সবচেয়ে latest complete backup — Infinite Scroll, SEO, Analytics সবকিছু আছে।"
