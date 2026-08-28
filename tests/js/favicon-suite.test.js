const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

const specPath = path.join(__dirname, '../../specs/favicon-suite.spec.json');
const spec = JSON.parse(fs.readFileSync(specPath, 'utf8'));
const templatePath = path.join(__dirname, '../../templates/tools/favicon_suite.html.twig');
const template = fs.readFileSync(templatePath, 'utf8');

assert.ok(spec.supported_input_formats.includes('image/svg+xml'));
assert.ok(spec.output_bundle_files.favicon_svg);
assert.ok(spec.output_bundle_files.favicon_ico);
assert.ok(spec.output_bundle_files.apple_touch_icon);
assert.ok(spec.output_bundle_files.icon_192);
assert.ok(spec.output_bundle_files.icon_512);
assert.ok(spec.output_bundle_files.icon_maskable_512);
assert.ok(spec.output_bundle_files.webmanifest);
assert.equal(spec.output_bundle_files.icon_maskable_512.safe_zone_inset_ratio, 0.1);

assert.ok(spec.recommended_html_tags.some(t => t.includes('favicon.ico')));
assert.ok(spec.recommended_html_tags.some(t => t.includes('favicon.svg')));
assert.ok(spec.recommended_html_tags.some(t => t.includes('apple-touch-icon.png')));
assert.ok(spec.recommended_html_tags.some(t => t.includes('site.webmanifest')));

assert.ok(spec.presets.some(p => p.id === 'monochrome_inverting_logo'));
assert.ok(spec.presets.some(p => p.id === 'dual_color_brand'));
assert.ok(spec.presets.some(p => p.id === 'solid_badge_pwa'));
assert.match(template, /caddy-transpiler\.css/, 'Favicon Suite must load its workspace layout stylesheet');

console.log('Favicon Suite JS spec tests passed');
