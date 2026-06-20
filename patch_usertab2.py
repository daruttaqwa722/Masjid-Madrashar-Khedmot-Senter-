f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
with open(f, 'r', encoding='utf-8') as fp:
    c = fp.read()

# 1) Button text স্পষ্ট করি
old_tabs = '''<div style="display:flex;gap:0.5rem;margin-top:0.75rem;flex-wrap:wrap;" id="userTabBtns">
  <button id="uTabAll" onclick="setUserTab('all')" class="tab-btn active" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">সকল</button>
  <button id="uTabActive" onclick="setUserTab('active')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">✅ সক্রিয়</button>
  <button id="uTabExpired" onclick="setUserTab('expired')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">❌ মেয়াদ শেষ</button>
  <button id="uTabPending" onclick="setUserTab('pending')" class="tab-btn" style="padding:0.35rem 0.9rem;font-size:0.82rem;border-radius:20px;">⏳ রেজিস্ট্রেশন</button>
</div>'''

new_tabs = '''<div style="display:flex;gap:0.5rem;margin-top:0.75rem;flex-wrap:wrap;" id="userTabBtns">
  <button id="uTabAll" onclick="setUserTab('all')" class="tab-btn active" style="padding:0.4rem 1rem;font-size:0.85rem;border-radius:20px;font-weight:700;">সকল</button>
  <button id="uTabActive" onclick="setUserTab('active')" class="tab-btn" style="padding:0.4rem 1rem;font-size:0.85rem;border-radius:20px;font-weight:700;">সক্রিয়</button>
  <button id="uTabExpired" onclick="setUserTab('expired')" class="tab-btn" style="padding:0.4rem 1rem;font-size:0.85rem;border-radius:20px;font-weight:700;">মেয়াদ শেষ</button>
  <button id="uTabPending" onclick="setUserTab('pending')" class="tab-btn" style="padding:0.4rem 1rem;font-size:0.85rem;border-radius:20px;font-weight:700;">রেজিস্ট্রেশন</button>
</div>'''

# 2) রেজিস্ট্রেশন filter — plain_pass নেই যাদের
old_filter = "else if (currentUserTab === 'pending') users = allUsers.filter(u => !u.expiresAt || u.expiresAt == 0);"
new_filter = "else if (currentUserTab === 'pending') users = allUsers.filter(u => !u.plain_pass || u.plain_pass === '');"

changes = [
    (old_tabs, new_tabs, 'button text'),
    (old_filter, new_filter, 'pending filter'),
]

for old, new, name in changes:
    if old in c:
        c = c.replace(old, new, 1)
        print('ok:', name)
    else:
        print('NOT FOUND:', name)

with open(f, 'w', encoding='utf-8') as fp:
    fp.write(c)
print('done')
