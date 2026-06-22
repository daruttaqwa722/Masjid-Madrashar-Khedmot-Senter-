f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

insert_before = 'function admIsExpired'

new_fn = '''async function showUserPass(id, btn) {
  const span = document.getElementById('pass_'+id);
  if (btn.dataset.loaded) {
    span.style.display = span.style.display==='inline' ? 'none' : 'inline';
    btn.textContent = span.style.display==='inline' ? '🙈 লুকান' : '👁 পাসওয়ার্ড';
    return;
  }
  const res = await api('admin_get_password', {id});
  if (res.success) {
    span.textContent = res.plain_pass || '(সেট নেই)';
    span.style.display = 'inline';
    btn.textContent = '🙈 লুকান';
    btn.dataset.loaded = '1';
  } else {
    alert('পাসওয়ার্ড পাওয়া যায়নি');
  }
}
'''

if insert_before in c:
    c = c.replace(insert_before, new_fn + insert_before, 1)
    open(f, 'w', encoding='utf-8').write(c)
    print('done')
else:
    print('NOT FOUND')
