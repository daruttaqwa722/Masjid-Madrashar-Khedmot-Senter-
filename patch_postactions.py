f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

insert_before = 'function admIsExpired'

new_fns = '''function openEditPost(id, content) {
  document.getElementById('editPostId').value = id;
  document.getElementById('editPostContent').value = content;
  document.getElementById('editPostModal').classList.add('open');
}
function closeEditPost() { document.getElementById('editPostModal').classList.remove('open'); }
async function saveEditPost() {
  const id=document.getElementById('editPostId').value, content=document.getElementById('editPostContent').value.trim();
  if (!content) return;
  const res = await api('admin_edit_post', {id: String(id), content});
  if (res.success) { closeEditPost(); admLoadPosts(); newsLoaded=false; }
}
async function deletePost(id) {
  if (!confirm('এই পোস্ট মুছে ফেলবেন?')) return;
  const res = await api('admin_delete_post', {id});
  if (res.success) { document.getElementById('postItem'+id)?.remove(); newsLoaded=false; admLoadPosts(); }
}
'''

if insert_before in c:
    c = c.replace(insert_before, new_fns + insert_before, 1)
    open(f, 'w', encoding='utf-8').write(c)
    print('done')
else:
    print('NOT FOUND')
