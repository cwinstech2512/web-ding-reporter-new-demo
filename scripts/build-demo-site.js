'use strict'

const fs = require('fs')
const path = require('path')

const root = path.resolve(__dirname, '..')
const publicDir = path.join(root, 'public')
const outputDir = path.join(root, 'pages-dist')
const manifestPath = path.join(publicDir, 'mix-manifest.json')

if (!fs.existsSync(manifestPath)) {
  throw new Error('public/mix-manifest.json was not generated')
}

const manifest = JSON.parse(fs.readFileSync(manifestPath, 'utf8'))
const cssPath = manifest['/dist/css/app.css']
const jsPath = manifest['/dist/js/app.js']

if (!cssPath || !jsPath) {
  throw new Error('Demo assets are missing from public/mix-manifest.json')
}

fs.rmSync(outputDir, { recursive: true, force: true })
fs.mkdirSync(outputDir, { recursive: true })

for (const directory of ['dist', 'assets', 'fonts']) {
  const source = path.join(publicDir, directory)
  if (fs.existsSync(source)) fs.cpSync(source, path.join(outputDir, directory), { recursive: true })
}

for (const file of ['favicon.ico', 'robots.txt']) {
  const source = path.join(publicDir, file)
  if (fs.existsSync(source)) fs.copyFileSync(source, path.join(outputDir, file))
}

const html = `<!doctype html>
<html lang="zh-TW">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <meta name="description" content="祥鼎工程檢測報告系統前端展示版">
  <title>祥鼎工程檢測報告系統｜前端展示</title>
  <link rel="icon" href="./favicon.ico">
  <link rel="stylesheet" href=".${cssPath}">
</head>
<body>
  <noscript>此展示網站需要啟用 JavaScript。</noscript>
  <div id="app"></div>
  <script>window.config={appName:'祥鼎工程檢測報告系統',locale:'zh-CN',locales:{'zh-CN':'中文'},githubAuth:null};</script>
  <script src=".${jsPath}"></script>
</body>
</html>
`

fs.writeFileSync(path.join(outputDir, 'index.html'), html)
fs.writeFileSync(path.join(outputDir, '404.html'), html)
fs.writeFileSync(path.join(outputDir, '.nojekyll'), '')

console.log(`Frontend demo prepared in ${path.relative(root, outputDir)}/`)
