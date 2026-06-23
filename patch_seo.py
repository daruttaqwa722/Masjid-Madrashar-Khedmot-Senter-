import subprocess
import datetime

# ১. robots.txt তৈরি
robots = """User-agent: *
Allow: /
Disallow: /api.php
Disallow: /patch_*

Sitemap: https://khedmotcenter.com/sitemap.xml
"""

with open("robots.txt", "w", encoding="utf-8") as f:
    f.write(robots)
print("robots.txt তৈরি হয়েছে")

# ২. MySQL থেকে সব active পোস্ট নিয়ে sitemap তৈরি
result = subprocess.run(
    ["mysql", "-u", "khedmotuser", "-pkhedmot@722", "khedmotdb",
     "-e", "SELECT slug, created FROM posts WHERE status='active' ORDER BY created DESC;",
     "--skip-column-names", "--batch"],
    capture_output=True, text=True
)

lines = [l.strip() for l in result.stdout.strip().split("\n") if l.strip()]

urls = []
for line in lines:
    parts = line.split("\t")
    if len(parts) >= 2:
        slug = parts[0].strip()
        created_ms = parts[1].strip()
        try:
            dt = datetime.datetime.fromtimestamp(int(created_ms) / 1000)
            lastmod = dt.strftime("%Y-%m-%d")
        except:
            lastmod = "2026-01-01"
        urls.append((slug, lastmod))

print(f"মোট পোস্ট পাওয়া গেছে: {len(urls)}")

# sitemap.xml তৈরি
sitemap = '<?xml version="1.0" encoding="UTF-8"?>\n'
sitemap += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n'
sitemap += '<url><loc>https://khedmotcenter.com/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>\n'

for slug, lastmod in urls:
    sitemap += f'<url><loc>https://khedmotcenter.com/post/{slug}</loc><lastmod>{lastmod}</lastmod><changefreq>weekly</changefreq><priority>0.8</priority></url>\n'

sitemap += '</urlset>'

with open("sitemap.xml", "w", encoding="utf-8") as f:
    f.write(sitemap)

print(f"sitemap.xml তৈরি হয়েছে - মোট {len(urls)+1} URL")
print("সব কাজ শেষ!")
