/**
 * Apple Universal Links (AASA) & Android App Links Validator
 * Privacy-First Client-Side Evaluation Engine
 */
(function(root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.AppLinksValidator = factory();
    if (typeof document !== 'undefined') {
      document.addEventListener('DOMContentLoaded', function() {
        root.AppLinksValidator.initUI();
      });
    }
  }
})(typeof self !== 'undefined' ? self : this, function() {
  'use strict';

  const APP_ID_REGEX = /^[A-Z0-9]{10}\.[a-zA-Z0-9_.-]+$/;
  const ANDROID_FINGERPRINT_REGEX = /^([0-9A-F]{2}:){31}[0-9A-F]{2}$/;
  const ANDROID_REQUIRED_RELATION = 'delegate_permission/common.handle_all_urls';

  const DIAGNOSTIC_CODES = {
    ERR_AASA_NOT_FOUND: {
      severity: 'error',
      title: 'Apple AASA File Unreachable',
      description: 'The apple-app-site-association file is missing or returns a non-200 HTTP status code.'
    },
    ERR_AASA_REDIRECT_FORBIDDEN: {
      severity: 'error',
      title: 'HTTP Redirect Forbidden on AASA',
      description: 'Apple CDN and iOS strictly forbid HTTP 301/302 redirects when fetching AASA files.'
    },
    ERR_AASA_INVALID_JSON: {
      severity: 'error',
      title: 'Malformed AASA JSON',
      description: 'The apple-app-site-association file is not valid JSON.'
    },
    ERR_AASA_MISSING_DETAILS: {
      severity: 'error',
      title: 'AASA App Links Rules Missing',
      description: "The AASA document must contain a non-empty 'applinks.details' array."
    },
    ERR_AASA_MISSING_APP_ID: {
      severity: 'error',
      title: 'AASA App Identifier Missing',
      description: "Each AASA detail must declare at least one appID or appIDs value."
    },
    ERR_AASA_MISSING_ROUTES: {
      severity: 'error',
      title: 'AASA Route Rules Missing',
      description: 'Each AASA detail must declare a non-empty components or paths array.'
    },
    ERR_AASA_INVALID_APP_ID: {
      severity: 'error',
      title: 'Invalid Apple App ID Format',
      description: 'AppID must consist of a 10-character Team ID followed by a dot and Bundle Identifier (e.g. ABCDE12345.com.example.app).'
    },
    ERR_AASA_SIZE_EXCEEDED: {
      severity: 'error',
      title: 'AASA Exceeds Maximum Size Limit',
      description: 'File exceeds Apple\'s 128KB limit for uncompressed AASA manifests.'
    },
    ERR_ASSETLINKS_NOT_FOUND: {
      severity: 'error',
      title: 'Android AssetLinks File Missing',
      description: 'The /.well-known/assetlinks.json file is unreachable or returns a non-200 HTTP status.'
    },
    ERR_ASSETLINKS_INVALID_JSON: {
      severity: 'error',
      title: 'Malformed AssetLinks JSON',
      description: 'The assetlinks.json file is not valid JSON.'
    },
    ERR_ASSETLINKS_EMPTY: {
      severity: 'error',
      title: 'AssetLinks Statements Missing',
      description: 'The assetlinks.json document must contain at least one statement.'
    },
    ERR_ASSETLINKS_INVALID_TARGET: {
      severity: 'error',
      title: 'Android App Target Incomplete',
      description: 'Each statement must identify an Android package and at least one certificate fingerprint.'
    },
    ERR_NO_MANIFEST_INPUT: {
      severity: 'error',
      title: 'No Manifest Provided',
      description: 'Paste an AASA document, an assetlinks.json document, or use the live URL check.'
    },
    ERR_TEST_URL_INVALID: {
      severity: 'error',
      title: 'Invalid Test URL',
      description: 'Use a complete HTTPS URL for live verification, or a path beginning with / for offline simulation.'
    },
    ERR_ASSETLINKS_MISSING_RELATION: {
      severity: 'error',
      title: 'Missing Required AssetLinks Relation',
      description: 'Android App Links require \'delegate_permission/common.handle_all_urls\' in the relation array.'
    },
    ERR_ASSETLINKS_INVALID_FINGERPRINT: {
      severity: 'error',
      title: 'Invalid SHA-256 Fingerprint Format',
      description: 'Android certificate fingerprints must be 32 colon-separated two-digit uppercase hex values.'
    },
    WARN_LEGACY_AASA_PATHS: {
      severity: 'warning',
      title: 'Legacy Paths Array Used',
      description: 'Using legacy \'paths\' array instead of modern iOS 13+ \'components\' dictionary with pattern matching.'
    },
    WARN_CONTENT_TYPE_MISMATCH: {
      severity: 'warning',
      title: 'Non-Standard Content-Type',
      description: 'Server returned text/plain or text/html instead of application/json.'
    },
    INFO_ROUTE_MATCHED_APP: {
      severity: 'info',
      title: 'URL Matches an App Route Rule',
      description: 'The URL path matches an included AASA rule. Actual app opening also depends on the installed app, entitlements, association state, and device context.'
    },
    INFO_ROUTE_FALLS_BACK_WEB: {
      severity: 'info',
      title: 'No Included AASA Route Match',
      description: 'No included AASA path rule matched, or an exclusion matched.'
    }
  };

  function matchPathPattern(path, pattern) {
    if (!path.startsWith('/')) path = '/' + path;
    const escaped = pattern.replace(/[.+^${}()|[\]\\]/g, '\\$&');
    const regexStr = '^' + escaped.replace(/\*/g, '.*').replace(/\?/g, '.') + '$';
    return new RegExp(regexStr).test(path);
  }

  function escapeHtml(value) {
    return String(value).replace(/[&<>"']/g, character => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);
  }

  function validateAppLinks(aasaInput, assetLinksInput, testUrl, expectedDomain) {
    const diagnostics = [];
    const aasaAppIds = [];
    const androidPackageNames = [];
    let aasaValid = true;
    let assetLinksValid = true;
    let opensInApp = null;
    let matchedPattern = null;
    let matchedExclusion = false;
    let routeScopeVerified = false;
    const hasAasaInput = (aasaInput !== null && typeof aasaInput === 'object') || (typeof aasaInput === 'string' && aasaInput.trim() !== '');
    const hasAssetLinksInput = (assetLinksInput !== null && typeof assetLinksInput === 'object') || (typeof assetLinksInput === 'string' && assetLinksInput.trim() !== '');

    if (!hasAasaInput && !hasAssetLinksInput) {
      diagnostics.push({ code: 'ERR_NO_MANIFEST_INPUT', ...DIAGNOSTIC_CODES.ERR_NO_MANIFEST_INPUT });
    }

    // 1. Parse AASA
    let aasa = null;
    if (aasaInput) {
      if (typeof aasaInput === 'string') {
        try {
          aasa = JSON.parse(aasaInput);
        } catch {
          aasaValid = false;
          diagnostics.push({ code: 'ERR_AASA_INVALID_JSON', ...DIAGNOSTIC_CODES.ERR_AASA_INVALID_JSON });
        }
      } else if (typeof aasaInput === 'object') {
        aasa = aasaInput;
      }
    }

    if (aasa) {
      const details = aasa.applinks?.details;
      if (!Array.isArray(details) || details.length === 0) {
        aasaValid = false;
        diagnostics.push({ code: 'ERR_AASA_MISSING_DETAILS', ...DIAGNOSTIC_CODES.ERR_AASA_MISSING_DETAILS });
      } else {
        for (const detail of details) {
          if (!detail || typeof detail !== 'object' || Array.isArray(detail)) {
            aasaValid = false;
            diagnostics.push({ code: 'ERR_AASA_MISSING_ROUTES', ...DIAGNOSTIC_CODES.ERR_AASA_MISSING_ROUTES });
            continue;
          }
          const ids = Array.isArray(detail.appIDs)
            ? detail.appIDs
            : (detail.appID ? [detail.appID] : []);

          if (ids.length === 0) {
            aasaValid = false;
            diagnostics.push({ code: 'ERR_AASA_MISSING_APP_ID', ...DIAGNOSTIC_CODES.ERR_AASA_MISSING_APP_ID });
          }

          for (const id of ids) {
            if (typeof id === 'string') {
              aasaAppIds.push(id);
              if (!APP_ID_REGEX.test(id)) {
                aasaValid = false;
                diagnostics.push({ code: 'ERR_AASA_INVALID_APP_ID', ...DIAGNOSTIC_CODES.ERR_AASA_INVALID_APP_ID });
              }
            } else {
              aasaValid = false;
              diagnostics.push({ code: 'ERR_AASA_INVALID_APP_ID', ...DIAGNOSTIC_CODES.ERR_AASA_INVALID_APP_ID });
            }
          }

          if (detail.paths && !detail.components) {
            diagnostics.push({ code: 'WARN_LEGACY_AASA_PATHS', ...DIAGNOSTIC_CODES.WARN_LEGACY_AASA_PATHS });
          }
          if ((!Array.isArray(detail.components) || detail.components.length === 0) &&
              (!Array.isArray(detail.paths) || detail.paths.length === 0)) {
            aasaValid = false;
            diagnostics.push({ code: 'ERR_AASA_MISSING_ROUTES', ...DIAGNOSTIC_CODES.ERR_AASA_MISSING_ROUTES });
          }
        }
      }

      // Test URL Routing
      if (testUrl && aasaValid) {
        let parsedPath = '/';
        try {
          const isPathOnly = testUrl.startsWith('/');
          const urlObj = new URL(isPathOnly ? 'https://offline.invalid' + testUrl : testUrl);
          if (!isPathOnly && urlObj.protocol !== 'https:') throw new Error('HTTPS required');
          if (expectedDomain && urlObj.hostname.toLowerCase() !== expectedDomain.toLowerCase()) {
            throw new Error('Domain mismatch');
          }
          parsedPath = urlObj.pathname || '/';
          routeScopeVerified = Boolean(expectedDomain && !isPathOnly);
        } catch {
          aasaValid = false;
          diagnostics.push({ code: 'ERR_TEST_URL_INVALID', ...DIAGNOSTIC_CODES.ERR_TEST_URL_INVALID });
          parsedPath = null;
        }

        let evaluated = false;
        if (parsedPath !== null && Array.isArray(details)) {
          for (const detail of details) {
            if (Array.isArray(detail.components)) {
              for (const comp of detail.components) {
                const pattern = comp['/'];
                if (typeof pattern === 'string' && matchPathPattern(parsedPath, pattern)) {
                  matchedPattern = pattern;
                  if (comp.exclude === true) {
                    opensInApp = false;
                    matchedExclusion = true;
                    diagnostics.push({ code: 'INFO_ROUTE_FALLS_BACK_WEB', ...DIAGNOSTIC_CODES.INFO_ROUTE_FALLS_BACK_WEB });
                  } else {
                    opensInApp = true;
                    matchedExclusion = false;
                    diagnostics.push({ code: 'INFO_ROUTE_MATCHED_APP', ...DIAGNOSTIC_CODES.INFO_ROUTE_MATCHED_APP });
                  }
                  evaluated = true;
                  break;
                }
              }
            }
            if (evaluated) break;
          }
        }

        if (parsedPath !== null && !evaluated) {
          opensInApp = false;
          diagnostics.push({ code: 'INFO_ROUTE_FALLS_BACK_WEB', ...DIAGNOSTIC_CODES.INFO_ROUTE_FALLS_BACK_WEB });
        }
      }
    }

    // 2. Parse AssetLinks
    let assetLinks = null;
    if (assetLinksInput) {
      if (typeof assetLinksInput === 'string') {
        try {
          assetLinks = JSON.parse(assetLinksInput);
        } catch {
          assetLinksValid = false;
          diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_JSON', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_JSON });
        }
      } else if (typeof assetLinksInput === 'object') {
        assetLinks = assetLinksInput;
      }
    }

    if (assetLinks) {
      const statements = Array.isArray(assetLinks) ? assetLinks : [assetLinks];
      if (statements.length === 0) {
        assetLinksValid = false;
        diagnostics.push({ code: 'ERR_ASSETLINKS_EMPTY', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_EMPTY });
      }
      for (const statement of statements) {
        if (!statement || typeof statement !== 'object' || Array.isArray(statement)) {
          assetLinksValid = false;
          diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_TARGET', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_TARGET });
          continue;
        }

        const rels = Array.isArray(statement.relation) ? statement.relation : [statement.relation];
        if (!rels.includes(ANDROID_REQUIRED_RELATION)) {
          assetLinksValid = false;
          diagnostics.push({ code: 'ERR_ASSETLINKS_MISSING_RELATION', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_MISSING_RELATION });
        }

        if (statement.target) {
          if (statement.target.package_name) {
            androidPackageNames.push(statement.target.package_name);
          }
          const fps = Array.isArray(statement.target.sha256_cert_fingerprints)
            ? statement.target.sha256_cert_fingerprints
            : [statement.target.sha256_cert_fingerprints];

          if (statement.target.namespace !== 'android_app' ||
              typeof statement.target.package_name !== 'string' ||
              statement.target.package_name.trim() === '' ||
              fps.length === 0 || fps[0] == null) {
            assetLinksValid = false;
            diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_TARGET', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_TARGET });
          }

          for (const fp of fps) {
            if (typeof fp !== 'string' || !ANDROID_FINGERPRINT_REGEX.test(fp)) {
              assetLinksValid = false;
              diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_FINGERPRINT', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_FINGERPRINT });
            }
          }
        } else {
          assetLinksValid = false;
          diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_TARGET', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_TARGET });
        }
      }
    }

    const hasErrors = diagnostics.some((d) => d.severity === 'error');

    return {
      isValid: !hasErrors && aasaValid && assetLinksValid,
      opensInApp,
      matchedPattern,
      matchedExclusion,
      diagnostics,
      aasaValid,
      assetLinksValid,
      aasaAppIds: Array.from(new Set(aasaAppIds)),
      androidPackageNames: Array.from(new Set(androidPackageNames)),
      testUrl,
      routeScopeVerified
    };
  }

  function initUI() {
    const aasaEditor = document.getElementById('aasa-editor');
    const assetlinksEditor = document.getElementById('assetlinks-editor');
    const testUrlInput = document.getElementById('test-url-input');
    const validateBtn = document.getElementById('btn-validate-local-app-links');
    const presetButtons = document.querySelectorAll('.btn-preset[data-preset]');

    if (!aasaEditor && !testUrlInput) return;

    function runClientValidation() {
      const aasaVal = aasaEditor ? aasaEditor.value : '';
      const assetlinksVal = assetlinksEditor ? assetlinksEditor.value : '';
      const testUrlVal = testUrlInput ? testUrlInput.value : '';

      const result = validateAppLinks(aasaVal, assetlinksVal, testUrlVal);
      renderUIResults(result);
    }

    function renderUIResults(result) {
      const routingBanner = document.getElementById('routing-decision-banner');
      if (routingBanner) {
        if (result.aasaValid && result.opensInApp === true) {
          routingBanner.className = 'routing-banner banner-app';
          routingBanner.innerHTML = `
            <div class="banner-badge">PATH RULE MATCH</div>
            <div class="banner-title">The pasted AASA rules include this path</div>
            <div class="banner-desc">Matched pattern: <code>${escapeHtml(result.matchedPattern || '/*')}</code></div>
          `;
        } else if (result.aasaValid && result.opensInApp === false) {
          routingBanner.className = 'routing-banner banner-web';
          routingBanner.innerHTML = `
            <div class="banner-badge">NO AASA MATCH</div>
            <div class="banner-title">No included AASA route matches this path</div>
            <div class="banner-desc">${result.matchedExclusion ? `Explicitly excluded by rule: <code>${escapeHtml(result.matchedPattern)}</code>` : 'No matching universal link pattern found.'}</div>
          `;
        } else {
          routingBanner.className = 'routing-banner';
          routingBanner.innerHTML = '<div class="banner-title">Check a valid AASA document and HTTPS path to see its route match.</div>';
        }
      }

      const diagContainer = document.getElementById('app-links-diagnostics');
      if (diagContainer) {
        if (result.diagnostics.length === 0) {
          diagContainer.innerHTML = '<div class="diag-item diag-success">All checks for the provided manifest data passed.</div>';
        } else {
          diagContainer.innerHTML = result.diagnostics.map((d) => `
            <div class="diag-item diag-${d.severity}">
              <strong>${d.title}</strong>
              <p>${d.description}</p>
            </div>
          `).join('');
        }
      }
    }

    if (validateBtn) {
      validateBtn.addEventListener('click', runClientValidation);
    }
    if (aasaEditor) {
      aasaEditor.addEventListener('input', runClientValidation);
    }
    if (assetlinksEditor) {
      assetlinksEditor.addEventListener('input', runClientValidation);
    }

    if (presetButtons) {
      presetButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const aasaData = btn.getAttribute('data-aasa');
          const assetlinksData = btn.getAttribute('data-assetlinks');
          const testUrl = btn.getAttribute('data-test-url');
          if (aasaData && aasaEditor) {
            aasaEditor.value = aasaData;
          }
          if (testUrl && testUrlInput) {
            testUrlInput.value = testUrl;
          }
          if (assetlinksEditor) {
            assetlinksEditor.value = assetlinksData || '';
          }
          runClientValidation();
        });
      });
    }
  }

  return {
    validateAppLinks,
    DIAGNOSTIC_CODES,
    initUI
  };
});
