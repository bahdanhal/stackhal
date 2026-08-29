const assert = require('node:assert');
const fs = require('node:fs');
const path = require('node:path');

const css = fs.readFileSync(path.join(__dirname, '../../public/page-remix.css'), 'utf8');
const paperColor = '#f7f0de';

function ruleFor(selector) {
  const escapedSelector = selector.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  const match = css.match(new RegExp(`${escapedSelector}\\s*\\{([^}]*)\\}`));

  assert.ok(match, `Expected CSS rule for ${selector}`);

  return match[1];
}

function propertyFrom(rule, property) {
  const match = rule.match(new RegExp(`${property}:\\s*([^;]+)`));

  assert.ok(match, `Expected ${property} declaration`);

  return match[1].trim().replace(/\s*!important$/, '');
}

function relativeLuminance(hexColor) {
  const channels = hexColor.match(/[0-9a-f]{2}/gi).map((channel) => parseInt(channel, 16) / 255);
  const linearChannels = channels.map((channel) => (
    channel <= 0.04045 ? channel / 12.92 : ((channel + 0.055) / 1.055) ** 2.4
  ));

  return (0.2126 * linearChannels[0]) + (0.7152 * linearChannels[1]) + (0.0722 * linearChannels[2]);
}

function contrastRatio(foreground, background) {
  const foregroundLuminance = relativeLuminance(foreground);
  const backgroundLuminance = relativeLuminance(background);
  const lighter = Math.max(foregroundLuminance, backgroundLuminance);
  const darker = Math.min(foregroundLuminance, backgroundLuminance);

  return (lighter + 0.05) / (darker + 0.05);
}

const listRule = ruleFor('.page-blog-article .article-content li');
const linkRule = ruleFor('.page-blog-article .article-content a');
const sourceRule = ruleFor('.page-blog-article .article-sources');

assert.ok(
  contrastRatio(propertyFrom(listRule, 'color'), paperColor) >= 7,
  'Blog list text must meet WCAG AAA contrast against the article paper background.'
);
assert.ok(
  contrastRatio(propertyFrom(linkRule, 'color'), paperColor) >= 7,
  'Blog links must meet WCAG AAA contrast against the article paper background.'
);
assert.ok(
  contrastRatio(propertyFrom(sourceRule, 'color'), paperColor) >= 7,
  'Blog source text must meet WCAG AAA contrast against the article paper background.'
);
assert.strictEqual(propertyFrom(listRule, 'font-size'), '18px');

console.log('Blog article readability test suite passed cleanly.');
