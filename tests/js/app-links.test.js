const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

// Load client-side validator module
const validatorPath = path.resolve(__dirname, '../../public/app-links.js');
const { validateAppLinks, DIAGNOSTIC_CODES } = require(validatorPath);

// Load specification
const specPath = path.resolve(__dirname, '../../specs/app-links.spec.json');
const spec = JSON.parse(fs.readFileSync(specPath, 'utf8'));

// Verify diagnostic codes exist in spec
assert.ok(spec.diagnostic_codes);
for (const code of Object.keys(spec.diagnostic_codes)) {
  assert.ok(DIAGNOSTIC_CODES[code], `Diagnostic code ${code} must exist in DIAGNOSTIC_CODES`);
}

// Verify test vectors with client-side validator
for (const vector of spec.test_vectors) {
  const result = validateAppLinks(vector.manifest, null, vector.test_url);

  if (vector.expected_opens_in_app !== undefined) {
    assert.equal(
      result.opensInApp,
      vector.expected_opens_in_app,
      `opensInApp mismatch for vector: ${vector.description}`
    );
  }

  if (vector.expected_matched_pattern !== undefined) {
    assert.equal(
      result.matchedPattern,
      vector.expected_matched_pattern,
      `matchedPattern mismatch for vector: ${vector.description}`
    );
  }

  if (vector.expected_matched_exclusion !== undefined) {
    assert.equal(
      result.matchedExclusion,
      vector.expected_matched_exclusion,
      `matchedExclusion mismatch for vector: ${vector.description}`
    );
  }

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
}

// Additional test: Android AssetLinks validation
const validAssetLinks = [
  {
    relation: ['delegate_permission/common.handle_all_urls'],
    target: {
      namespace: 'android_app',
      package_name: 'com.example.app',
      sha256_cert_fingerprints: [
        '14:6D:E9:DE:0F:45:79:F6:10:5A:12:60:2B:93:FC:7F:16:17:D6:31:02:61:00:EC:4F:60:9E:78:21:C6:0F:C0'
      ]
    }
  }
];

const assetResult = validateAppLinks(null, validAssetLinks);
assert.equal(assetResult.isValid, true);
assert.equal(assetResult.assetLinksValid, true);
assert.deepEqual(assetResult.androidPackageNames, ['com.example.app']);

// Broken Android AssetLinks missing relation
const brokenAssetLinks = [
  {
    relation: ['other_permission'],
    target: {
      namespace: 'android_app',
      package_name: 'com.example.app',
      sha256_cert_fingerprints: ['BAD_FINGERPRINT']
    }
  }
];
const brokenResult = validateAppLinks(null, brokenAssetLinks);
assert.equal(brokenResult.isValid, false);
assert.equal(brokenResult.assetLinksValid, false);
assert.ok(brokenResult.diagnostics.some((d) => d.code === 'ERR_ASSETLINKS_MISSING_RELATION'));
assert.ok(brokenResult.diagnostics.some((d) => d.code === 'ERR_ASSETLINKS_INVALID_FINGERPRINT'));

console.log('Apple Universal Links & Android App Links JS test suite passed cleanly.');
