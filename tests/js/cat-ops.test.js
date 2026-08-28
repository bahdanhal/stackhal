const assert = require('node:assert/strict');
const { createActivationGate, pointDistance } = require('../../public/cat-ops.js');

const gate = createActivationGate(3, 900);
assert.equal(gate(100), false);
assert.equal(gate(450), false);
assert.equal(gate(800), true);

assert.equal(gate(2000), false);
assert.equal(gate(3001), false, 'Expired clicks must not activate CatOps');
assert.equal(gate(3100), false);
assert.equal(gate(3200), true);

assert.equal(pointDistance({ x: 0, y: 0 }, { x: 3, y: 4 }), 5);

console.log('CatOps easter egg test suite passed cleanly.');
