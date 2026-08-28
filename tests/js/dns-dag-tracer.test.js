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
assert.ok(spec.supported_query_types.includes('DS'));
assert.ok(spec.supported_query_types.includes('DNSKEY'));

assert.equal(spec.delegation_hierarchy.length, 4);
assert.equal(spec.delegation_hierarchy[0].name, 'root');
assert.equal(spec.delegation_hierarchy[1].name, 'tld');
assert.equal(spec.delegation_hierarchy[2].name, 'authoritative');
assert.equal(spec.delegation_hierarchy[3].name, 'edge_resolvers');

assert.ok(spec.diagnostic_codes.ERR_LAME_DELEGATION);
assert.ok(spec.diagnostic_codes.ERR_DNSSEC_BOGUS);
assert.ok(spec.diagnostic_codes.ERR_MISSING_GLUE_RECORD);
assert.ok(spec.diagnostic_codes.ERR_NXDOMAIN);
assert.ok(spec.diagnostic_codes.WARN_SOA_SERIAL_DIVERGENCE);
assert.ok(spec.diagnostic_codes.WARN_HIGH_TTL_MIGRATION_RISK);
assert.ok(spec.diagnostic_codes.INFO_DNSSEC_SECURE);
assert.ok(spec.diagnostic_codes.ERR_LIVE_TRACE_UNAVAILABLE);

console.log('DNS Propagation DAG Tracer JS spec tests passed');
