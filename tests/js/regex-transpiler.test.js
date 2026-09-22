const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');

// Load client-side transpiler module
const transpilerPath = path.resolve(__dirname, '../../public/regex-transpiler.js');
const { transpileRegex, ENGINES, DIAGNOSTIC_CODES, parseRegexInput, applySourceFlags } = require(transpilerPath);

// Load specification
const specPath = path.resolve(__dirname, '../../specs/regex-transpiler.spec.json');
const spec = JSON.parse(fs.readFileSync(specPath, 'utf8'));

// Verify supported engines
assert.ok(Array.isArray(spec.supported_engines));
for (const engine of spec.supported_engines) {
  assert.ok(ENGINES[engine], `Engine ${engine} must exist in ENGINES`);
}

// Verify test vectors with client-side transpiler
for (const vector of spec.test_vectors) {
  const result = transpileRegex(vector.pattern, vector.source_engine, vector.target_engine);

  assert.equal(
    result.isCompatible,
    vector.expected_compatible,
    `Compatibility mismatch for vector: ${vector.description}`
  );

  if (vector.expected_pattern !== undefined) {
    assert.equal(
      result.transpiledPattern,
      vector.expected_pattern,
      `Pattern mismatch for vector: ${vector.description}`
    );
  }

  if (vector.expected_warnings) {
    const warningCodes = result.warnings.map((w) => w.code || Object.keys(DIAGNOSTIC_CODES).find(k => DIAGNOSTIC_CODES[k].title === w.title));
    for (const expectedWarning of vector.expected_warnings) {
      assert.ok(
        result.warnings.some(
          (w) => w.title === DIAGNOSTIC_CODES[expectedWarning]?.title
        ),
        `Missing expected warning ${expectedWarning} in vector: ${vector.description}`
      );
    }
  }

  if (vector.expected_error_codes) {
    for (const expectedError of vector.expected_error_codes) {
      assert.ok(
        result.errors.some(
          (e) => e.title === DIAGNOSTIC_CODES[expectedError]?.title
        ),
        `Missing expected error ${expectedError} in vector: ${vector.description}`
      );
    }
  }
}

// Additional test: Matrix generation across 5 engines
const complexPattern = '(?<user>[a-zA-Z0-9_]+)(?=admin)';
const matrixResult = transpileRegex(complexPattern, 'pcre', 'go_re2');
assert.equal(matrixResult.matrix.length, 5);

const pcreComp = matrixResult.matrix.find((m) => m.engine === 'pcre');
const goComp = matrixResult.matrix.find((m) => m.engine === 'go_re2');
const jsComp = matrixResult.matrix.find((m) => m.engine === 'javascript');
const pyComp = matrixResult.matrix.find((m) => m.engine === 'python');
const rustComp = matrixResult.matrix.find((m) => m.engine === 'rust');

assert.ok(pcreComp.isCompatible);
assert.ok(jsComp.isCompatible);
assert.ok(pyComp.isCompatible);
assert.ok(!goComp.isCompatible);
assert.ok(!rustComp.isCompatible);

const malformed = transpileRegex('(abc', 'pcre', 'javascript');
assert.equal(malformed.isCompatible, false);
assert.ok(malformed.errors.some((error) => error.title === DIAGNOSTIC_CODES.ERR_INVALID_PATTERN.title));

const inlineFlags = transpileRegex('(?i)^hello$', 'pcre', 'javascript');
assert.equal(inlineFlags.isCompatible, false);
assert.ok(inlineFlags.errors.some(
  (error) => error.title === DIAGNOSTIC_CODES.ERR_INLINE_MODIFIERS_REQUIRE_EXTERNAL_FLAGS.title
));

const javascriptLiteral = parseRegexInput('/(?<name>foo)+/i');
assert.equal(javascriptLiteral.sourceEngine, 'javascript');
assert.equal(javascriptLiteral.flags, 'i');
assert.equal(parseRegexInput('(abc').sourceEngine, null);

const flaggedGo = applySourceFlags(transpileRegex(javascriptLiteral.pattern, 'javascript', 'go_re2'), javascriptLiteral.flags);
assert.equal(flaggedGo.isCompatible, false);
assert.ok(flaggedGo.errors.some((error) => error.code === 'ERR_JAVASCRIPT_FLAGS_NOT_TRANSLATED'));
assert.equal(flaggedGo.matrix.find((item) => item.engine === 'go_re2').isCompatible, false);

const flaggedJavaScript = applySourceFlags(transpileRegex(javascriptLiteral.pattern, 'javascript', 'javascript'), javascriptLiteral.flags);
assert.equal(flaggedJavaScript.isCompatible, true);
assert.ok(flaggedJavaScript.diagnostics.some((diagnostic) => diagnostic.code === 'INFO_JAVASCRIPT_FLAGS_IN_SNIPPET'));

console.log('Regex Dialect Transpiler JS test suite passed cleanly.');
