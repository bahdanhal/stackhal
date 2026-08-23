(function() {
  'use strict';

  function initDnsDagTracer() {
    const domainInput = document.getElementById('domain-input');
    const queryTypeSelect = document.getElementById('query-type-select');
    const form = document.getElementById('dns-form');
    const presetButtons = document.querySelectorAll('.btn-preset[data-domain]');

    if (!domainInput) return;

    if (presetButtons) {
      presetButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const domain = btn.getAttribute('data-domain');
          const queryType = btn.getAttribute('data-query-type');
          if (domain && domainInput) {
            domainInput.value = domain;
          }
          if (queryType && queryTypeSelect) {
            queryTypeSelect.value = queryType;
          }
          if (form) {
            form.submit();
          }
        });
      });
    }
  }

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initDnsDagTracer };
  } else if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDnsDagTracer);
  } else {
    initDnsDagTracer();
  }
})();
