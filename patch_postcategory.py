f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

# 1) Form এ category dropdown যোগ
old_form = '''          <div class="form-group">
            <label class="form-label">পোস্টের বিবরণ <span style="color:red">*</span></label>
            <textarea class="form-textarea" id="postContent" rows="8" placeholder="এখানে নিয়োগ বিজ্ঞপ্তির সম্পূর্ণ লেখা পেস্ট করুন…" style="min-height:160px;"></textarea>
          </div>'''

new_form = '''          <div class="form-group">
            <label class="form-label">বিভাগ <span style="color:red">*</span></label>
            <select class="form-input" id="postCategory" style="height:44px;cursor:pointer;">
              <option value="mosque">🕌 মসজিদের নিয়োগ বিজ্ঞপ্তি</option>
              <option value="male-madrasa">📚 পুরুষ মাদ্রাসার নিয়োগ</option>
              <option value="female-madrasa">🎓 মহিলা মাদ্রাসার নিয়োগ</option>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">পোস্টের বিবরণ <span style="color:red">*</span></label>
            <textarea class="form-textarea" id="postContent" rows="8" placeholder="এখানে নিয়োগ বিজ্ঞপ্তির সম্পূর্ণ লেখা পেস্ট করুন…" style="min-height:160px;"></textarea>
          </div>'''

# 2) submitPost এ category নেওয়া
old_submit = "  const content = document.getElementById('postContent').value.trim();"
new_submit = "  const content = document.getElementById('postContent').value.trim();\n  const category = document.getElementById('postCategory')?.value || 'mosque';"

# 3) api call এ category পাঠানো
old_api = "await api('admin_create_post', {"
new_api = "await api('admin_create_post', {category,"

changes = [
    (old_form, new_form, 'category dropdown'),
    (old_submit, new_submit, 'category var'),
    (old_api, new_api, 'category in api'),
]

for old, new, name in changes:
    if old in c:
        c = c.replace(old, new, 1)
        print('ok:', name)
    else:
        print('NOT FOUND:', name)

open(f, 'w', encoding='utf-8').write(c)
print('done')
