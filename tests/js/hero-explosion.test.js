const assert = require('node:assert/strict');
const { createBlastProfile } = require('../../public/hero-explosion.js');

let value = 0;
const profile = createBlastProfile(() => {
  value = (value + .173) % 1;
  return value;
});

assert.equal(profile.fireEdge.length, 64);
assert.equal(profile.smokeEdge.length, 52);
assert.equal(profile.embers.length, 34);
assert.ok(profile.fireEdge.every((point) => point >= .78 && point <= 1.06));
assert.ok(profile.embers.every((ember) => ember.distance > 0 && ember.length > 0));

console.log('Hero single-blast test suite passed cleanly.');
