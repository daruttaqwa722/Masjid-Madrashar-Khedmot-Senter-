with open("index.html", "r", encoding="utf-8") as f:
    content = f.read()

# Replace logo.png icon with favicon.ico + add 512 png
old = '<link rel="icon" type="image/png" href="logo.png">'
new = '<link rel="icon" href="favicon.ico">\n<link rel="icon" type="image/png" sizes="512x512" href="favicon-512.png">'
content = content.replace(old, new)

# Fix OG image
content = content.replace(
    'content="https://khedmotcenter.com/favicon.png"',
    'content="https://khedmotcenter.com/favicon-512.png"'
)

with open("index.html", "w", encoding="utf-8") as f:
    f.write(content)

print("Done!")
