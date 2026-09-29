'use strict'

const fs = require('fs')
const path = require('path')

const root = path.resolve(__dirname, '..')
const sourceRoot = path.join(root, 'resources', 'js')
const failures = []

function exactExists (target) {
  const absolute = path.resolve(target)
  const parsed = path.parse(absolute)
  const segments = absolute.slice(parsed.root.length).split(path.sep).filter(Boolean)
  let current = parsed.root

  for (const segment of segments) {
    if (!fs.existsSync(current)) return false
    if (fs.readdirSync(current).indexOf(segment) < 0) return false
    current = path.join(current, segment)
  }

  return true
}

function resolveLocal (sourceFile, request) {
  if (!(request.indexOf('.') === 0 || request.indexOf('~/') === 0)) return true
  const base = request.indexOf('~/') === 0
    ? path.join(sourceRoot, request.slice(2))
    : path.resolve(path.dirname(sourceFile), request)
  const candidates = [
    base,
    base + '.js',
    base + '.vue',
    base + '.json',
    path.join(base, 'index.js'),
    path.join(base, 'index.vue')
  ]
  return candidates.some(exactExists)
}

function visit (directory) {
  fs.readdirSync(directory).forEach(name => {
    const file = path.join(directory, name)
    const stat = fs.statSync(file)
    if (stat.isDirectory()) return visit(file)
    if (!/\.(js|vue|css|scss|sass)$/.test(name)) return

    const source = fs.readFileSync(file, 'utf8')
      .replace(/<!--[\s\S]*?-->/g, '')
      .replace(/\/\*[\s\S]*?\*\//g, '')
      .replace(/^\s*\/\/.*$/gm, '')

    const importPattern = /(?:from\s*|import\s*|require\(\s*|require\(\s*\[\s*)['"]([^'"]+)['"]/g
    let match
    while ((match = importPattern.exec(source))) {
      if (!resolveLocal(file, match[1])) failures.push(path.relative(root, file) + ': import ' + match[1])
    }

    const urlPattern = /url\(\s*['"]?([^)'"\s]+)['"]?\s*\)/g
    while ((match = urlPattern.exec(source))) {
      const request = match[1].split(/[?#]/)[0]
      if (request.indexOf('.') !== 0 || request.indexOf('{') >= 0) continue
      if (!exactExists(path.resolve(path.dirname(file), request))) {
        failures.push(path.relative(root, file) + ': url ' + match[1])
      }
    }

    const assetPattern = /\b(?:src|poster|xlink:href)\s*=\s*['"]([^'"]+)['"]/g
    while ((match = assetPattern.exec(source))) {
      const request = match[1].split(/[?#]/)[0]
      if (!(request.indexOf('.') === 0 || request.indexOf('~/') === 0)) continue
      if (!resolveLocal(file, request)) failures.push(path.relative(root, file) + ': asset ' + match[1])
    }
  })
}

visit(sourceRoot)

if (failures.length) {
  console.error('Case-sensitive path verification failed:\n' + failures.join('\n'))
  process.exit(1)
}

console.log('Verified local imports and static assets with exact filesystem casing.')
