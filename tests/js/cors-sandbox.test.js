const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

// Load client-side CORS sandbox module
const sandboxPath = path.resolve(__dirname, '../../public/cors-sandbox.js');
const { analyzeCors, DIAGNOSTIC_CODES, BROWSER_MAX_AGE_CEILINGS } = require(sandboxPath);

// Load specification
const specPath = path.resolve(__dirname, '../../specs/cors-sandbox.spec.json');
const spec = JSON.parse(fs.readFileSync(specPath, 'utf8'));

// Verify diagnostic codes exist in spec
assert.ok(spec.diagnostic_codes);
for (const code of Object.keys(spec.diagnostic_codes)) {
  assert.ok(DIAGNOSTIC_CODES[code], `Diagnostic code ${code} must exist in DIAGNOSTIC_CODES`);
}

// Verify browser max age ceilings
assert.equal(BROWSER_MAX_AGE_CEILINGS.chromium, 7200);
assert.equal(BROWSER_MAX_AGE_CEILINGS.firefox, 86400);
assert.equal(BROWSER_MAX_AGE_CEILINGS.safari, 600);

// Verify test vectors with client-side sandbox
for (const vector of spec.test_vectors) {
  const result = analyzeCors(vector.request, vector.response);

  if (vector.expected_valid !== undefined) {
    assert.equal(
      result.isValid,
      vector.expected_valid,
      `isValid mismatch for vector: ${vector.description}`
    );
  }

  if (vector.expected_error_codes) {
    const errorCodes = result.diagnostics
      .filter((d) => d.severity === 'error')
      .map((d) => d.code);

    for (const expectedError of vector.expected_error_codes) {
      assert.ok(
        errorCodes.includes(expectedError),
        `Missing expected error code ${expectedError} in vector: ${vector.description}`
      );
    }
  }

  if (vector.expected_warning_codes) {
    const warningCodes = result.diagnostics
      .filter((d) => d.severity === 'warning')
      .map((d) => d.code);

    for (const expectedWarning of vector.expected_warning_codes) {
      assert.ok(
        warningCodes.includes(expectedWarning),
        `Missing expected warning code ${expectedWarning} in vector: ${vector.description}`
      );
    }
  }
}

// Additional test: Missing allow origin
const missingOriginResult = analyzeCors({ origin: 'https://foo.com' }, {});
assert.equal(missingOriginResult.isValid, false);
assert.ok(missingOriginResult.diagnostics.some((d) => d.code === 'ERR_CORS_MISSING_ALLOW_ORIGIN'));

console.log('CORS & Preflight Sandbox JS test suite passed cleanly.');
