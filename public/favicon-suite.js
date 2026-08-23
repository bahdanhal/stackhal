(function() {
  'use strict';

  function initFaviconSuite() {
    const svgInput = document.getElementById('svg-input');
    const strategySelect = document.getElementById('dark_mode_strategy');
    const clearBtn = document.getElementById('btn-clear-svg');
    const copyHtmlBtn = document.getElementById('btn-copy-html');
    const htmlTagsCode = document.getElementById('html-tags-code');
    const tabIconLight = document.getElementById('tab-icon-light');
    const tabIconDark = document.getElementById('tab-icon-dark');
    const presetButtons = document.querySelectorAll('.btn-preset[data-preset-id]');

    if (!svgInput) return;

    function updatePreview() {
      const svg = svgInput.value.trim();
      if (!svg || !svg.includes('<svg')) {
        if (tabIconLight) tabIconLight.innerHTML = '';
        if (tabIconDark) tabIconDark.innerHTML = '';
        return;
      }

      if (tabIconLight) {
        tabIconLight.innerHTML = svg;
      }

      if (tabIconDark) {
        const strategy = strategySelect ? strategySelect.value : 'css_invert_fill';
        let darkSvg = svg;
        if (strategy === 'css_invert_fill' && !svg.includes('prefers-color-scheme')) {
          darkSvg = svg.replace('<svg', '<svg style="filter: invert(1) hue-rotate(180deg);"');
        }
        tabIconDark.innerHTML = darkSvg;
      }
    }

    if (presetButtons) {
      presetButtons.forEach(btn => {
        btn.addEventListener('click', () => {
          const sampleSvg = btn.getAttribute('data-svg');
          const strategy = btn.getAttribute('data-strategy');
          if (sampleSvg && svgInput) {
            svgInput.value = sampleSvg;
          }
          if (strategy && strategySelect) {
            strategySelect.value = strategy;
          }
          updatePreview();
        });
      });
    }

    if (svgInput) {
      svgInput.addEventListener('input', updatePreview);
    }

    if (strategySelect) {
      strategySelect.addEventListener('change', updatePreview);
    }

    if (clearBtn && svgInput) {
      clearBtn.addEventListener('click', () => {
        svgInput.value = '';
        updatePreview();
      });
    }

    if (copyHtmlBtn && htmlTagsCode) {
      copyHtmlBtn.addEventListener('click', async () => {
        try {
          await navigator.clipboard.writeText(htmlTagsCode.innerText.trim());
          const originalText = copyHtmlBtn.innerText;
          copyHtmlBtn.innerText = '✓ Copied!';
          setTimeout(() => {
            copyHtmlBtn.innerText = originalText;
          }, 2000);
        } catch (err) {
          console.error('Failed to copy', err);
        }
      });
    }

    updatePreview();
  }

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = { initFaviconSuite };
  } else if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFaviconSuite);
  } else {
    initFaviconSuite();
  }
})();
