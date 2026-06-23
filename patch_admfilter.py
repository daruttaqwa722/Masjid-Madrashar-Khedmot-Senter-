with open("index.html", "r", encoding="utf-8") as f:
    content = f.read()

old = "async function applyFilter(containerId) {\n  // User Dashboard এর জন্য আলাদা handler\n  if (containerId === 'udMainContent') { await applyFilterUD(containerId); return; }"

new = """async function applyFilter(containerId) {
  // Admin postsContainer এর জন্য আলাদা handler
  if (containerId === 'postsContainer') { await applyFilterAdm(containerId); return; }
  // User Dashboard এর জন্য আলাদা handler
  if (containerId === 'udMainContent') { await applyFilterUD(containerId); return; }"""

if old in content:
    content = content.replace(old, new)
    print("Step 1 OK: applyFilter handler যোগ হয়েছে")
else:
    print("Step 1 FAIL: পুরনো কোড পাওয়া যায়নি")

# applyFilterAdm ফাংশন যোগ করি admLoadPosts এর আগে
adm_func = """
async function applyFilterAdm(containerId) {
  if (!window._activeFilter) window._activeFilter = {};
  if (!window._activeFilter[containerId]) window._activeFilter[containerId] = {hours: 72, cat: ''};
  const hours = window._activeFilter[containerId].hours || 72;
  const cat = window._activeFilter[containerId].cat || '';
  const c = document.getElementById(containerId);
  if (!c) return;
  c.querySelectorAll('.post-item, .post-card, .empty').forEach(el => el.style.opacity = '0.4');
  const params = {limit: 10, offset: 0, hours: hours};
  if (cat) params.category = cat;
  const res = await api('admin_get_posts', params);
  if (!res.success) { c.querySelectorAll('.post-item, .post-card').forEach(el => el.style.opacity = '1'); return; }
  const catMap = {'mosque-jobs':'মসজিদের নিয়োগ','male-madrasa-jobs':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa-jobs':'মহিলা মাদ্রাসার নিয়োগ','mosque':'মসজিদের নিয়োগ','male-madrasa':'পুরুষ মাদ্রাসার নিয়োগ','female-madrasa':'মহিলা মাদ্রাসার নিয়োগ'};
  const html = res.posts.length ? res.posts.map(p => `
    <div class="post-item" id="postItem${p.id}">
      <div class="post-item-date">${admFmtDt(p.created)}
        <div class="post-item-actions">
          <button class="btn btn-warning btn-sm" onclick="openEditPost('${p.id}', \\`${admEsc(p.content).replace(/\\`/g,"'")}\\`)">✏️</button>
          <button class="btn btn-danger btn-sm" onclick="deletePost('${p.id}')">🗑️</button>
        </div>
      </div>
      <div class="post-item-body">${(Array.isArray(p.cats)&&p.cats.length?p.cats:[p.category]).filter(Boolean).map(cc=>`<span style="background:#e6f4ea;color:#1a7a3c;font-size:0.75rem;padding:2px 10px;border-radius:20px;font-weight:600;margin-right:4px;">${catMap[cc]||cc}</span>`).join('')}
      ${p.position?`<div style="font-size:0.8rem;color:#1a7a3c;font-weight:600;margin-bottom:2px;">${admEsc(p.position)}</div>`:""}
      ${p.address?`<div style="font-size:0.78rem;color:#888;margin-bottom:4px;">📍 ${admEsc(p.address)}</div>`:""}
      ${p.title?`<div style="font-weight:700;color:#05057D;font-size:1rem;margin-bottom:4px;">${admEsc(p.title)}</div>`:""}
      <div class="adm-post-text">${admEsc(p.content)}</div>
      ${p.image_path?`<img class="adm-post-img" src="${p.image_path}" loading="lazy">`:''}
      </div>
    </div>`).join('') : '<div class="adm-empty">এই ফিল্টারে কোনো পোস্ট নেই।</div>';
  c.querySelectorAll('.post-item, .post-card, .empty, .adm-empty').forEach(el => el.remove());
  const sentinel = document.getElementById('scroll_sentinel_postsContainer');
  if (sentinel) sentinel.remove();
  c.insertAdjacentHTML('beforeend', html);
  window._admScrollState = {offset: res.posts.length, done: res.posts.length < 10, loading: false, hours: hours, cat: cat};
}

"""

old2 = "async function admLoadPosts() {"
if old2 in content:
    content = content.replace(old2, adm_func + old2)
    print("Step 2 OK: applyFilterAdm ফাংশন যোগ হয়েছে")
else:
    print("Step 2 FAIL: admLoadPosts পাওয়া যায়নি")

with open("index.html", "w", encoding="utf-8") as f:
    f.write(content)

print("সব কাজ শেষ!")
