f = '/home/khedmotcenter/htdocs/khedmotcenter.com/index.html'
c = open(f, 'r', encoding='utf-8').read()

# 1) পুরনো checkbox block সরাই
old_checkbox = '''          <div class="form-group">
            <label class="form-label">ক্যাটাগরি <span style="color:red">*</span></label>
            <div id="admCatError" style="display:none;color:red;font-size:0.78rem;margin-bottom:0.4rem;">কমপক্ষে একটি ক্যাটাগরি সিলেক্ট করুন।</div>
            <div style="display:flex;flex-direction:column;gap:8px;">              <label style="display:flex;align-items:center;gap:10px;padding:0.75rem 1rem;border:1.5px solid var(--border);border-radius:12px;cursor:pointer;background:#f8f9ff;font-size:0.9rem;font-weight:600;color:#05057D;">
                <input type="checkbox" value="mosque-jobs" class="admCatChk" style="width:18px;height:18px;accent-color:#05057D;cursor:pointer;flex-shrink:0;"> মসজিদের নিয়োগ বিজ্ঞপ্তি
              </label>
              <label style="display:flex;align-items:center;gap:10px;padding:0.75rem 1rem;border:1.5px solid var(--border);border-radius:12px;cursor:pointer;background:#f8f9ff;font-size:0.9rem;font-weight:600;color:#05057D;">
                <input type="checkbox" value="male-madrasa-jobs" class="admCatChk" style="width:18px;height:18px;accent-color:#05057D;cursor:pointer;flex-shrink:0;"> পুরুষ মাদ্রাসার নিয়োগ
              </label>
              <label style="display:flex;align-items:center;gap:10px;padding:0.75rem 1rem;border:1.5px solid var(--border);border-radius:12px;cursor:pointer;background:#f8f9ff;font-size:0.9rem;font-weight:600;color:#05057D;">
                <input type="checkbox" value="female-madrasa-jobs" class="admCatChk" style="width:18px;height:18px;accent-color:#05057D;cursor:pointer;flex-shrink:0;"> মহিলা মাদ্রাসার নিয়োগ
              </label>
            </div>
          </div>'''
new_checkbox = ''

# 2) নতুন dropdown এ emoji সরাই
old_dropdown = '''          <div class="form-group">
            <label class="form-label">বিভাগ <span style="color:red">*</span></label>
            <select class="form-input" id="postCategory" style="height:44px;cursor:pointer;">
              <option value="mosque">🕌 মসজিদের নিয়োগ বিজ্ঞপ্তি</option>
              <option value="male-madrasa">📚 পুরুষ মাদ্রাসার নিয়োগ</option>
              <option value="female-madrasa">🎓 মহিলা মাদ্রাসার নিয়োগ</option>
            </select>
          </div>'''
new_dropdown = '''          <div class="form-group">
            <label class="form-label">বিভাগ <span style="color:red">*</span></label>
            <select class="form-input" id="postCategory" style="height:44px;cursor:pointer;">
              <option value="">-- বিভাগ সিলেক্ট করুন --</option>
              <option value="mosque">মসজিদের নিয়োগ বিজ্ঞপ্তি</option>
              <option value="male-madrasa">পুরুষ মাদ্রাসার নিয়োগ</option>
              <option value="female-madrasa">মহিলা মাদ্রাসার নিয়োগ</option>
            </select>
          </div>'''

# 3) submitPost এ validation যোগ
old_submit = "  const content = document.getElementById('postContent').value.trim();\n  const category = document.getElementById('postCategory')?.value || 'mosque';"
new_submit = "  const content = document.getElementById('postContent').value.trim();\n  const category = document.getElementById('postCategory')?.value || '';\n  if (!category) { admShowAlert('postAlert', 'বিভাগ সিলেক্ট করুন।'); return; }"

changes = [
    (old_checkbox, new_checkbox, 'remove checkbox'),
    (old_dropdown, new_dropdown, 'remove emoji + add blank option'),
    (old_submit, new_submit, 'category validation'),
]

for old, new, name in changes:
    if old in c:
        c = c.replace(old, new, 1)
        print('ok:', name)
    else:
        print('NOT FOUND:', name)

open(f, 'w', encoding='utf-8').write(c)
print('done')
