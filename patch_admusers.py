f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

# admLoadUsers, setUserTab, admSearchUsers যোগ করব
# খুঁজি কোথায় যোগ করব — admIsExpired এর আগে
insert_before = 'function admIsExpired'

new_fns = '''let currentUserTab = 'all';
let admSearchTimer;
function setUserTab(tab) {
  currentUserTab = tab;
  ['all','active','expired','pending'].forEach(t => {
    const id = 'uTab' + t.charAt(0).toUpperCase() + t.slice(1);
    const btn = document.getElementById(id);
    if (btn) btn.classList.toggle('active', t === tab);
  });
  admLoadUsers();
}
function admSearchUsers() { clearTimeout(admSearchTimer); admSearchTimer = setTimeout(admLoadUsers, 400); }
async function admLoadUsers() {
  const search = document.getElementById('searchInput')?.value?.trim() || '';
  const res = await api('admin_get_users', search ? {search} : {});
  const c = document.getElementById('usersContainer');
  if (!res.success) { c.innerHTML = '<div class="adm-empty">লোড করা যায়নি।</div>'; return; }
  const now = Date.now();
  let users = res.users || [];
  if (currentUserTab === 'active') users = users.filter(u => u.expiresAt && u.expiresAt > now);
  else if (currentUserTab === 'expired') users = users.filter(u => u.expiresAt && u.expiresAt <= now);
  else if (currentUserTab === 'pending') users = users.filter(u => !u.expiresAt || u.expiresAt == 0);
  if (!users.length) { c.innerHTML = `<div class="adm-empty">${search?'কোনো ইউজার পাওয়া যায়নি।':'এই বিভাগে কোনো ইউজার নেই।'}</div>`; return; }
  c.innerHTML = users.map(u => {
    const exp = admIsExpired(u.expiry_date);
    return `<div class="user-card-adm" id="userCard${u.id}">
      <div class="user-card-row"><div><div class="user-name">${admEsc(u.name)}</div><div class="user-mobile-adm">📱 ${u.mobile}</div><div class="user-address-adm">📍 ${admEsc(u.address)}</div></div>
      <div style="text-align:right"><span class="badge ${exp?'badge-expired':'badge-active'}">${exp?'Expired':'Active'}</span><div class="user-expiry" style="color:${exp?'var(--red)':'var(--green)'};margin-top:4px;">মেয়াদ: ${admFmtDate(u.expiry_date)}</div></div></div>
      <div class="user-actions"><button class="btn btn-primary btn-sm" onclick='openEditUser(${JSON.stringify(u)})'>✏️ সম্পাদনা</button><button class="btn btn-warning btn-sm" onclick='openExtend(${u.id},"${admEsc(u.name)}","${u.mobile}","${u.expiry_date}")'>${u.expiresAt ? Math.ceil((u.expiresAt - Date.now()) / 86400000) + ' দিন বাকি' : 'মেয়াদ নেই'}</button><button class="btn btn-sm" style="background:#6c757d;color:#fff;" onclick="showUserPass('${u.id}',this)">👁 পাসওয়ার্ড</button><span id="pass_${u.id}" style="display:none;font-size:13px;background:#fff3cd;padding:2px 8px;border-radius:4px;margin-left:4px;"></span><button class="btn btn-danger btn-sm" onclick="deleteUser('${u.id}')">🗑️ মুছুন</button></div>
    </div>`;
  }).join('');
}
'''

if insert_before in c:
    c = c.replace(insert_before, new_fns + insert_before, 1)
    open(f, 'w', encoding='utf-8').write(c)
    print('done')
else:
    print('NOT FOUND: admIsExpired')
