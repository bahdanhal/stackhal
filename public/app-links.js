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
      title: 'URL Successfully Routes to Native App',
      description: 'The queried URL matches universal link pattern rules and will open inside the native application.'
    },
    INFO_ROUTE_FALLS_BACK_WEB: {
      severity: 'info',
      title: 'URL Opens in Safari / Browser Fallback',
      description: 'The queried URL does not match app route patterns or is explicitly excluded.'
    }
  };

  function matchPathPattern(path, pattern) {
    if (!path.startsWith('/')) path = '/' + path;
    const escaped = pattern.replace(/[.+^${}()|[\]\\]/g, '\\$&');
    const regexStr = '^' + escaped.replace(/\*/g, '.*').replace(/\?/g, '.') + '$';
    return new RegExp(regexStr).test(path);
  }

  function validateAppLinks(aasaInput, assetLinksInput, testUrl) {
    const diagnostics = [];
    const aasaAppIds = [];
    const androidPackageNames = [];
    let aasaValid = true;
    let assetLinksValid = true;
    let opensInApp = null;
    let matchedPattern = null;
    let matchedExclusion = false;

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
      if (Array.isArray(details)) {
        for (const detail of details) {
          const ids = Array.isArray(detail.appIDs)
            ? detail.appIDs
            : (detail.appID ? [detail.appID] : []);

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
        }
      }

      // Test URL Routing
      if (testUrl) {
        let parsedPath = '/';
        try {
          const urlObj = new URL(testUrl.startsWith('http') ? testUrl : 'https://example.com' + (testUrl.startsWith('/') ? '' : '/') + testUrl);
          parsedPath = urlObj.pathname || '/';
        } catch {
          parsedPath = testUrl.startsWith('/') ? testUrl : '/' + testUrl;
        }

        let evaluated = false;
        if (Array.isArray(details)) {
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

        if (!evaluated) {
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
          diagnostics.push({ code: 'ERR_ASSETLINKS_NOT_FOUND', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_NOT_FOUND });
        }
      } else if (typeof assetLinksInput === 'object') {
        assetLinks = assetLinksInput;
      }
    }

    if (assetLinks) {
      const statements = Array.isArray(assetLinks) ? assetLinks : [assetLinks];
      for (const statement of statements) {
        if (!statement || typeof statement !== 'object') continue;

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

          for (const fp of fps) {
            if (typeof fp !== 'string' || !ANDROID_FINGERPRINT_REGEX.test(fp)) {
              assetLinksValid = false;
              diagnostics.push({ code: 'ERR_ASSETLINKS_INVALID_FINGERPRINT', ...DIAGNOSTIC_CODES.ERR_ASSETLINKS_INVALID_FINGERPRINT });
            }
          }
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
      testUrl
    };
  }

  function initUI() {
    const aasaEditor = document.getElementById('aasa-editor');
    const assetlinksEditor = document.getElementById('assetlinks-editor');
    const testUrlInput = document.getElementById('test-url-input');
    const validateBtn = document.getElementById('btn-validate-app-links');
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
        if (result.opensInApp === true) {
          routingBanner.className = 'routing-banner banner-app';
          routingBanner.innerHTML = `
            <div class="banner-badge">APP ROUTE MATCH</div>
            <div class="banner-title">URL opens directly inside Native App</div>
            <div class="banner-desc">Matched pattern: <code>${result.matchedPattern || '/*'}</code></div>
          `;
        } else if (result.opensInApp === false) {
          routingBanner.className = 'routing-banner banner-web';
          routingBanner.innerHTML = `
            <div class="banner-badge">BROWSER FALLBACK</div>
            <div class="banner-title">URL opens in Safari / Browser</div>
            <div class="banner-desc">${result.matchedExclusion ? `Explicitly excluded by rule: <code>${result.matchedPattern}</code>` : 'No matching universal link pattern found.'}</div>
          `;
        }
      }

      const diagContainer = document.getElementById('app-links-diagnostics');
      if (diagContainer) {
        if (result.diagnostics.length === 0) {
          diagContainer.innerHTML = '<div class="diag-item diag-success">All Apple AASA & Android AssetLinks validation checks passed!</div>';
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
    if (testUrlInput) {
      testUrlInput.addEventListener('input', runClientValidation);
    }
    if (aasaEditor) {
      aasaEditor.addEventListener('input', runClientValidation);
    }

    if (presetButtons) {
      presetButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const aasaData = btn.getAttribute('data-aasa');
          const testUrl = btn.getAttribute('data-test-url');
          if (aasaData && aasaEditor) {
            aasaEditor.value = aasaData;
          }
          if (testUrl && testUrlInput) {
            testUrlInput.value = testUrl;
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
