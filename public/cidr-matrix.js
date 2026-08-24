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

    tabButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        const targetTab = btn.getAttribute('data-tab');

        tabButtons.forEach(b => b.classList.remove('active'));
        tabPanes.forEach(p => p.classList.remove('active'));

        btn.classList.add('active');
        const activePane = document.getElementById(targetTab);
        if (activePane) {
          activePane.classList.add('active');
        }
      });
    });
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
  }

  return {
    init: init,
    initTabs: initTabs,
    initMatrixCrosshair: initMatrixCrosshair
  };
});
