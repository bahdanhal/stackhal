/**
 * CORS & Preflight Diagnostic Sandbox
 * Privacy-First Client-Side Evaluation Engine
 */
(function(root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.CorsSandbox = factory();
    if (typeof document !== 'undefined') {
      document.addEventListener('DOMContentLoaded', function() {
        root.CorsSandbox.initUI();
      });
    }
  }
})(typeof self !== 'undefined' ? self : this, function() {
  'use strict';

  const BROWSER_MAX_AGE_CEILINGS = {
    chromium: 7200,
    firefox: 86400,
    safari: 600
  };

  const SAFELISTED_RESPONSE_HEADERS = [
    'cache-control',
    'content-language',
    'content-length',
    'content-type',
    'expires',
    'last-modified',
    'pragma',
    'vary',
    'access-control-allow-origin',
    'access-control-allow-methods',
    'access-control-allow-headers',
    'access-control-allow-credentials',
    'access-control-expose-headers',
    'access-control-max-age',
    'date',
    'server'
  ];

  const DIAGNOSTIC_CODES = {
    ERR_CORS_WILDCARD_WITH_CREDENTIALS: {
      severity: 'error',
      title: 'Wildcard Origin Forbidden with Credentials',
      description: 'The response specifies \'Access-Control-Allow-Origin: *\' while \'Access-Control-Allow-Credentials\' is set to \'true\'. Browsers reject this configuration for security reasons.'
    },
    ERR_CORS_ORIGIN_MISMATCH: {
      severity: 'error',
      title: 'Origin Mismatch',
      description: 'The \'Access-Control-Allow-Origin\' header value does not match the request \'Origin\'.'
    },
    ERR_CORS_MISSING_ALLOW_ORIGIN: {
      severity: 'error',
      title: 'Missing Access-Control-Allow-Origin',
      description: 'The server responded to a cross-origin request without an \'Access-Control-Allow-Origin\' header.'
    },
    ERR_CORS_METHOD_DISALLOWED: {
      severity: 'error',
      title: 'HTTP Method Disallowed by CORS',
      description: 'The requested HTTP method is not permitted in \'Access-Control-Allow-Methods\'.'
    },
    ERR_CORS_HEADER_DISALLOWED: {
      severity: 'error',
      title: 'Header Not Allowed by CORS',
      description: 'One or more custom request headers are not included in \'Access-Control-Allow-Headers\'.'
    },
    WARN_CORS_MISSING_VARY_ORIGIN: {
      severity: 'warning',
      title: 'Missing \'Vary: Origin\' Header',
      description: 'When dynamically reflecting request Origin, omitting \'Vary: Origin\' causes cache poisoning across CDNs and browser caches.'
    },
    WARN_CORS_EXCESSIVE_MAX_AGE: {
      severity: 'warning',
      title: 'Access-Control-Max-Age Exceeds Browser Ceiling',
      description: 'Max-Age exceeds browser maximum limits (Chromium caps at 2 hours / 7200s, Safari at 10 minutes / 600s).'
    },
    WARN_CORS_UNEXPOSED_CUSTOM_HEADERS: {
      severity: 'warning',
      title: 'Custom Response Headers Not Exposed',
      description: 'Custom response headers are present on the response but not declared in \'Access-Control-Expose-Headers\', preventing frontend JavaScript from reading them.'
    },
    INFO_CORS_PREFLIGHT_OK: {
      severity: 'info',
      title: 'Preflight OPTIONS Succeeded',
      description: 'Preflight handshake conforms to W3C Fetch specification.'
    }
  };

  function normalizeHeaders(headers) {
    const normalized = {};
    if (!headers) return normalized;

    if (Array.isArray(headers)) {
      for (const item of headers) {
        if (typeof item === 'string') {
          const idx = item.indexOf(':');
          if (idx !== -1) {
            normalized[item.slice(0, idx).trim().toLowerCase()] = item.slice(idx + 1).trim();
          } else {
            normalized[item.trim().toLowerCase()] = '';
          }
        }
      }
    } else if (typeof headers === 'object') {
      for (const [k, v] of Object.entries(headers)) {
        normalized[k.trim().toLowerCase()] = typeof v === 'string' ? v.trim() : String(v);
      }
    }
    return normalized;
  }

  function analyzeCors(requestInput, responseInput) {
    const diagnostics = [];
    const req = requestInput || {};
    const origin = (req.origin || '').trim();
    const method = (req.method || '').trim().toUpperCase();
    const withCredentials = req.with_credentials === true;
    const reqHeadersList = Array.isArray(req.headers)
      ? req.headers
      : (typeof req.headers === 'string' ? req.headers.split(',').map((s) => s.trim()).filter(Boolean) : []);

    const resHeaders = normalizeHeaders(responseInput);

    // 1. Check Access-Control-Allow-Origin
    const allowOrigin = resHeaders['access-control-allow-origin'];
    const allowCredentials = resHeaders['access-control-allow-credentials'] === 'true' || withCredentials;

    if (!allowOrigin) {
      diagnostics.push({ code: 'ERR_CORS_MISSING_ALLOW_ORIGIN', ...DIAGNOSTIC_CODES.ERR_CORS_MISSING_ALLOW_ORIGIN });
    } else {
      const trimmedAllowOrigin = allowOrigin.trim();

      if (trimmedAllowOrigin === '*' && allowCredentials) {
        diagnostics.push({ code: 'ERR_CORS_WILDCARD_WITH_CREDENTIALS', ...DIAGNOSTIC_CODES.ERR_CORS_WILDCARD_WITH_CREDENTIALS });
      } else if (trimmedAllowOrigin !== '*' && origin && trimmedAllowOrigin.toLowerCase() !== origin.toLowerCase()) {
        diagnostics.push({ code: 'ERR_CORS_ORIGIN_MISMATCH', ...DIAGNOSTIC_CODES.ERR_CORS_ORIGIN_MISMATCH });
      }

      // Check Vary: Origin
      if (trimmedAllowOrigin !== '*' && origin && trimmedAllowOrigin.toLowerCase() === origin.toLowerCase()) {
        const vary = resHeaders['vary'] || '';
        const varyTokens = vary.toLowerCase().split(',').map((s) => s.trim());
        if (!varyTokens.includes('origin') && !varyTokens.includes('*')) {
          diagnostics.push({ code: 'WARN_CORS_MISSING_VARY_ORIGIN', ...DIAGNOSTIC_CODES.WARN_CORS_MISSING_VARY_ORIGIN });
        }
      }
    }

    // 2. Check Access-Control-Allow-Methods
    if (method && method !== 'GET' && method !== 'HEAD' && method !== 'POST') {
      const allowMethods = resHeaders['access-control-allow-methods'];
      if (allowMethods) {
        const allowedMethodsList = allowMethods.toUpperCase().split(',').map((s) => s.trim());
        if (!allowedMethodsList.includes('*') && !allowedMethodsList.includes(method)) {
          diagnostics.push({ code: 'ERR_CORS_METHOD_DISALLOWED', ...DIAGNOSTIC_CODES.ERR_CORS_METHOD_DISALLOWED });
        }
      }
    }

    // 3. Check Access-Control-Allow-Headers
    if (reqHeadersList.length > 0) {
      const allowHeaders = resHeaders['access-control-allow-headers'];
      if (!allowHeaders) {
        diagnostics.push({ code: 'ERR_CORS_HEADER_DISALLOWED', ...DIAGNOSTIC_CODES.ERR_CORS_HEADER_DISALLOWED });
      } else {
        const allowedHeadersList = allowHeaders.toLowerCase().split(',').map((s) => s.trim());
        if (!allowedHeadersList.includes('*')) {
          for (const reqH of reqHeadersList) {
            if (!allowedHeadersList.includes(reqH.toLowerCase())) {
              diagnostics.push({ code: 'ERR_CORS_HEADER_DISALLOWED', ...DIAGNOSTIC_CODES.ERR_CORS_HEADER_DISALLOWED });
              break;
            }
          }
        }
      }
    }

    // 4. Check Access-Control-Max-Age
    if (resHeaders['access-control-max-age']) {
      const maxAge = parseInt(resHeaders['access-control-max-age'], 10);
      if (!isNaN(maxAge) && (maxAge > BROWSER_MAX_AGE_CEILINGS.chromium || maxAge > BROWSER_MAX_AGE_CEILINGS.safari)) {
        diagnostics.push({ code: 'WARN_CORS_EXCESSIVE_MAX_AGE', ...DIAGNOSTIC_CODES.WARN_CORS_EXCESSIVE_MAX_AGE });
      }
    }

    // 5. Check Custom Response Headers Exposure
    const exposeHeaders = (resHeaders['access-control-expose-headers'] || '').toLowerCase().split(',').map((s) => s.trim());
    const customUnexposed = [];
    for (const headerKey of Object.keys(resHeaders)) {
      if (!SAFELISTED_RESPONSE_HEADERS.includes(headerKey)) {
        if (!exposeHeaders.includes('*') && !exposeHeaders.includes(headerKey)) {
          customUnexposed.push(headerKey);
        }
      }
    }
    if (customUnexposed.length > 0) {
      diagnostics.push({ code: 'WARN_CORS_UNEXPOSED_CUSTOM_HEADERS', ...DIAGNOSTIC_CODES.WARN_CORS_UNEXPOSED_CUSTOM_HEADERS });
    }

    const hasErrors = diagnostics.some((d) => d.severity === 'error');
    if (!hasErrors) {
      diagnostics.push({ code: 'INFO_CORS_PREFLIGHT_OK', ...DIAGNOSTIC_CODES.INFO_CORS_PREFLIGHT_OK });
    }

    return {
      isValid: !hasErrors,
      diagnostics,
      requestHeaders: req,
      responseHeaders: resHeaders,
      browserCeilings: BROWSER_MAX_AGE_CEILINGS
    };
  }

  function initUI() {
    const originInput = document.getElementById('cors-origin-input');
    const methodSelect = document.getElementById('cors-method-select');
    const reqHeadersInput = document.getElementById('cors-req-headers-input');
    const credentialsCheckbox = document.getElementById('cors-credentials-check');
    const resHeadersEditor = document.getElementById('cors-response-headers');
    const analyzeBtn = document.getElementById('btn-analyze-cors');
    const presetButtons = document.querySelectorAll('.btn-preset[data-preset]');

    if (!originInput && !resHeadersEditor) return;

    function runClientAnalysis() {
      const origin = originInput ? originInput.value : '';
      const method = methodSelect ? methodSelect.value : 'GET';
      const reqHeaders = reqHeadersInput ? reqHeadersInput.value.split(',').map((s) => s.trim()).filter(Boolean) : [];
      const withCredentials = credentialsCheckbox ? credentialsCheckbox.checked : false;

      let resHeaders = {};
      if (resHeadersEditor) {
        const lines = resHeadersEditor.value.split('\n');
        for (const line of lines) {
          const idx = line.indexOf(':');
          if (idx !== -1) {
            resHeaders[line.slice(0, idx).trim()] = line.slice(idx + 1).trim();
          }
        }
      }

      const result = analyzeCors(
        { origin, method, headers: reqHeaders, with_credentials: withCredentials },
        resHeaders
      );
      renderUIResults(result);
    }

    function renderUIResults(result) {
      const banner = document.getElementById('cors-decision-banner');
      if (banner) {
        if (result.isValid) {
          banner.className = 'routing-banner banner-app';
          banner.innerHTML = `
            <div class="banner-badge">CORS VALID</div>
            <div class="banner-title">CORS Handshake Conforms to W3C Fetch Specification</div>
            <div class="banner-desc">Cross-origin requests permitted safely.</div>
          `;
        } else {
          banner.className = 'routing-banner banner-error';
          banner.innerHTML = `
            <div class="banner-badge">CORS BLOCKED</div>
            <div class="banner-title">Browser will Block Cross-Origin Request</div>
            <div class="banner-desc">One or more critical CORS security violations detected.</div>
          `;
        }
      }

      const diagContainer = document.getElementById('cors-diagnostics');
      if (diagContainer) {
        diagContainer.innerHTML = result.diagnostics.map((d) => `
          <div class="diag-item diag-${d.severity}">
            <strong>${d.title}</strong>
            <p>${d.description}</p>
          </div>
        `).join('');
      }
    }

    if (analyzeBtn) analyzeBtn.addEventListener('click', runClientAnalysis);
    if (originInput) originInput.addEventListener('input', runClientAnalysis);
    if (methodSelect) methodSelect.addEventListener('change', runClientAnalysis);
    if (reqHeadersInput) reqHeadersInput.addEventListener('input', runClientAnalysis);
    if (credentialsCheckbox) credentialsCheckbox.addEventListener('change', runClientAnalysis);
    if (resHeadersEditor) resHeadersEditor.addEventListener('input', runClientAnalysis);

    if (presetButtons) {
      presetButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
          const origin = btn.getAttribute('data-origin');
          const method = btn.getAttribute('data-method');
          const creds = btn.getAttribute('data-credentials') === 'true';
          const resH = btn.getAttribute('data-response-headers');

          if (origin && originInput) originInput.value = origin;
          if (method && methodSelect) methodSelect.value = method;
          if (credentialsCheckbox) credentialsCheckbox.checked = creds;
          if (resH && resHeadersEditor) {
            try {
              const parsed = JSON.parse(resH);
              resHeadersEditor.value = Object.entries(parsed).map(([k, v]) => `${k}: ${v}`).join('\n');
            } catch {
              resHeadersEditor.value = resH;
            }
          }
          runClientAnalysis();
        });
      });
    }
  }

  return {
    analyzeCors,
    DIAGNOSTIC_CODES,
    BROWSER_MAX_AGE_CEILINGS,
    initUI
  };
});
