const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

const root = path.resolve(__dirname, '../..');
const tool = fs.readFileSync(path.join(root, 'public/composer-license.js'), 'utf8');
const blog = fs.readFileSync(path.join(root, 'public/blog.js'), 'utf8');
const articleTemplate = fs.readFileSync(path.join(root, 'templates/blog/article.html.twig'), 'utf8');

assert.match(tool, /requires_review/);
assert.match(tool, /audit_mode/);
assert.match(tool, /scope_note/);
assert.doesNotMatch(tool, /is_tainted|VIRAL_COPYLEFT_CONTAMINATION/);
assert.match(blog, /navigator\.clipboard\.writeText/);
assert.match(blog, /data-copy-target/);
assert.match(articleTemplate, /<script src="\/blog\.js" defer><\/script>/);

console.log('Composer license and blog copy UI tests passed');
