const assert = require('node:assert/strict');
const {
  PUBLIC_SAFETY_MESSAGES,
  createSeededRandom,
  hashString,
  shuffledIndexes
} = require('../../public/psa-space-background.js');

assert.ok(PUBLIC_SAFETY_MESSAGES.length >= 8);
PUBLIC_SAFETY_MESSAGES.forEach((message) => {
  assert.equal(message.length, 2);
  assert.ok(message.every((line) => line.length > 10));
});

const firstRandom = createSeededRandom(hashString('/blog/example'));
const secondRandom = createSeededRandom(hashString('/blog/example'));
assert.deepEqual(
  [firstRandom(), firstRandom(), firstRandom()],
  [secondRandom(), secondRandom(), secondRandom()],
  'The same page must receive a stable background composition'
);

const messageOrder = shuffledIndexes(PUBLIC_SAFETY_MESSAGES.length, createSeededRandom(42));
assert.equal(new Set(messageOrder).size, PUBLIC_SAFETY_MESSAGES.length);
assert.notDeepEqual(
  shuffledIndexes(PUBLIC_SAFETY_MESSAGES.length, createSeededRandom(42)),
  shuffledIndexes(PUBLIC_SAFETY_MESSAGES.length, createSeededRandom(84)),
  'Different pages must receive different poster selections'
);

console.log('Public-safety space background test suite passed cleanly.');
