const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

const specPath = path.join(__dirname, '../../specs/dns-dag-tracer.spec.json');
const spec = JSON.parse(fs.readFileSync(specPath, 'utf8'));

assert.ok(spec.supported_query_types.includes('A'));
assert.ok(spec.supported_query_types.includes('AAAA'));
assert.ok(spec.supported_query_types.includes('CNAME'));
assert.ok(spec.supported_query_types.includes('TXT'));
assert.ok(spec.supported_query_types.includes('MX'));
assert.ok(spec.supported_query_types.includes('NS'));
assert.ok(spec.supported_query_types.includes('SOA'));
assert.ok(spec.supported_query_types.includes('CAA'));

assert.equal(spec.delegation_hierarchy.length, 2);
assert.equal(spec.delegation_hierarchy[0].name, 'resolver');
assert.equal(spec.delegation_hierarchy[1].name, 'authoritative');

assert.ok(spec.diagnostic_codes.ERR_INVALID_DOMAIN);
assert.ok(spec.diagnostic_codes.ERR_NO_RECORDS);
assert.ok(spec.diagnostic_codes.ERR_UNSUPPORTED_RECORD_TYPE);
assert.ok(spec.diagnostic_codes.INFO_LIVE_LOOKUP);

console.log('DNS Propagation DAG Tracer JS spec tests passed');
