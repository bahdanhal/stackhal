/**
 * Visual CIDR & Subnet Overlap Matrix Interactive Controller
 */
(function(root, factory) {
  if (typeof module === 'object' && module.exports) {
    module.exports = factory();
  } else {
    root.CidrMatrix = factory();
    if (typeof document !== 'undefined') {
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          root.CidrMatrix.init();
        });
      } else {
        root.CidrMatrix.init();
      }
    }
  }
})(typeof self !== 'undefined' ? self : this, function() {
  'use strict';

  function initTabs() {
    const tabButtons = document.querySelectorAll('.cidr-tab-btn[data-tab]');
    const tabPanes = document.querySelectorAll('.cidr-tab-pane');

    if (!tabButtons.length) return;

    function activateTab(btn) {
      const targetTab = btn.getAttribute('data-tab');

      tabButtons.forEach(b => {
        const selected = b === btn;
        b.classList.toggle('active', selected);
        b.setAttribute('aria-selected', String(selected));
        b.tabIndex = selected ? 0 : -1;
      });
      tabPanes.forEach(p => p.classList.remove('active'));

      const activePane = document.getElementById(targetTab);
      if (activePane) {
        activePane.classList.add('active');
      }
    }

    tabButtons.forEach((btn, index) => {
      btn.addEventListener('click', () => activateTab(btn));
      btn.addEventListener('keydown', event => {
        if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
        event.preventDefault();
        let nextIndex = index;
        if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabButtons.length;
        if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabButtons.length) % tabButtons.length;
        if (event.key === 'Home') nextIndex = 0;
        if (event.key === 'End') nextIndex = tabButtons.length - 1;
        activateTab(tabButtons[nextIndex]);
        tabButtons[nextIndex].focus();
      });
    });
  }

  function initAutomaticInput() {
    const form = document.getElementById('cidr-form');
    const textarea = document.getElementById('cidrs-textarea');
    const parent = document.getElementById('parent-cidr');
    const prefix = document.getElementById('free-prefix');

    if (!form || !textarea) return;

    document.querySelectorAll('.cidr-preset-btn[data-cidrs]').forEach(button => {
      button.addEventListener('click', () => {
        textarea.value = button.getAttribute('data-cidrs') || '';
        if (parent) parent.value = '';
        if (prefix) prefix.value = '';
        form.requestSubmit();
      });
    });

    textarea.addEventListener('paste', () => {
      window.setTimeout(() => {
        if (textarea.value.trim()) form.requestSubmit();
      }, 0);
    });

    const copyButton = document.getElementById('copy-recommended-cidr');
    const recommended = document.getElementById('recommended-cidr');
    if (copyButton && recommended) {
      copyButton.addEventListener('click', async () => {
        await navigator.clipboard.writeText(recommended.textContent.trim());
        copyButton.textContent = 'Copied';
      });
    }
  }

  function initMatrixCrosshair() {
    const cells = document.querySelectorAll('.matrix-cell[data-row][data-col]');
    const infoBox = document.getElementById('matrix-cell-info-display');
    const table = document.querySelector('.cidr-matrix-table');

    if (!cells.length || !table) return;

    const rowHeaders = table.querySelectorAll('th[data-row-header]');
    const colHeaders = table.querySelectorAll('th[data-col-header]');

    cells.forEach(cell => {
      cell.addEventListener('mouseenter', () => {
        const r = cell.getAttribute('data-row');
        const c = cell.getAttribute('data-col');
        const desc = cell.getAttribute('data-desc');
        const badge = cell.getAttribute('data-badge');
        const rowCidr = cell.getAttribute('data-row-cidr');
        const colCidr = cell.getAttribute('data-col-cidr');

        // Highlight headers
        rowHeaders.forEach(th => {
          if (th.getAttribute('data-row-header') === r) {
            th.style.background = '#2563eb';
            th.style.color = '#ffffff';
          }
        });
        colHeaders.forEach(th => {
          if (th.getAttribute('data-col-header') === c) {
            th.style.background = '#2563eb';
            th.style.color = '#ffffff';
          }
        });

        // Update info display if present
        if (infoBox && desc) {
          infoBox.innerHTML = `<strong>[${badge}]</strong> ${rowCidr} &harr; ${colCidr}: <span>${desc}</span>`;
        }
      });

      cell.addEventListener('mouseleave', () => {
        rowHeaders.forEach(th => {
          th.style.background = '';
          th.style.color = '';
        });
        colHeaders.forEach(th => {
          th.style.background = '';
          th.style.color = '';
        });
      });
    });
  }

  function init() {
    initTabs();
    initMatrixCrosshair();
    initAutomaticInput();
  }

  return {
    init: init,
    initTabs: initTabs,
    initMatrixCrosshair: initMatrixCrosshair,
    initAutomaticInput: initAutomaticInput
  };
});
