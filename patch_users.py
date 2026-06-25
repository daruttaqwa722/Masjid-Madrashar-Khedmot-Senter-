f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f, 'r', encoding='utf-8') as fp:
    c = fp.read()

# 1) Filter tabs যোগ — search box এর আগে
old_search = 'id="searchInput" placeholder="মোবাইল নাম্বার দিয়ে খুঁজুন" oninput="admSearchUsers()">'
new_search = '''id="searchInput" placeholder="মোবাইল নাম্বার দিয়ে খুঁজুন" oninput="admSearchUsers()">
<div style="display:flex;gap:0.5rem;margin-top:0.75rem;flex-wrap:wrap;" id="userTabBtns">
  <button id="uTabAll" onclick="setUserTab('all')" class="tab-btn active" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">সকল</button>
  <button id="uTabActive" onclick="setUserTab('active')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">✅ সক্রিয়</button>
  <button id="uTabExpired" onclick="setUserTab('expired')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">❌ মেয়াদ শেষ</button>
  <button id="uTabPending" onclick="setUserTab('pending')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">⏳ রেজিস্ট্রেশন</button>
</div>'''

# 2) admLoadUsers এর আগে setUserTab function যোগ
old_fn = 'function admSearchUsers() { clearTimeout(admSearchTimer); admSearchTimer = setTimeout(admLoadUsers, 400); }'
new_fn = '''let currentUserTab = 'all';
function setUserTab(tab) {
  currentUserTab = tab;
  ['all','active','expired','pending'].forEach(t => {
    const id = 'uTab' + t.charAt(0).toUpperCase() + t.slice(1);
    const btn = document.getElementById(id);
    if (btn) btn.classList.toggle('active', t === tab);
  });
  admLoadUsers();
}
function admSearchUsers() { clearTimeout(admSearchTimer); admSearchTimer = setTimeout(admLoadUsers, 400); }'''

# 3) admLoadUsers এ filter logic যোগ
old_load = '''  const res = await api('admin_get_users', search ? {search} : {});
  const c = document.getElementById('usersContainer');
  if (!res.success || !res.users.length) { c.innerHTML = `<div class="adm-empty">${search?'কোনো ইউজার পাওয়া যায়নি।':'কোনো ইউজার নেই।'}</div>`; return; }
  c.innerHTML = res.users.map(u => {'''

new_load = '''  const res = await api('admin_get_users', search ? {search} : {});
  const c = document.getElementById('usersContainer');
  if (!res.success) { c.innerHTML = '<div class="adm-empty">লোড করা যায়নি।</div>'; return; }
  const now = Date.now();
  let users = res.users || [];
  if (currentUserTab === 'active') users = users.filter(u => u.expiresAt && u.expiresAt > now);
  else if (currentUserTab === 'expired') users = users.filter(u => u.expiresAt && u.expiresAt <= now);
  else if (currentUserTab === 'pending') users = users.filter(u => !u.expiresAt || u.expiresAt == 0);
  if (!users.length) { c.innerHTML = `<div class="adm-empty">${search?'কোনো ইউজার পাওয়া যায়নি।':'এই বিভাগে কোনো ইউজার নেই।'}</div>`; return; }
  c.innerHTML = users.map(u => {'''

changes = [
    (old_search, new_search, 'filter tabs'),
    (old_fn, new_fn, 'setUserTab function'),
    (old_load, new_load, 'filter logic'),
]

ok = True
for old, new, name in changes:
    if old in c:
        c = c.replace(old, new, 1)
        print('ok:', name)
    else:
        print('NOT FOUND:', name)
        ok = False

with open(f, 'w', encoding='utf-8') as fp:
    fp.write(c)
print('done' if ok else 'partial - check above')
