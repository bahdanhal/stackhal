/**
 * Composer License & Copyleft Audit Interactive Tool
 */
document.addEventListener('DOMContentLoaded', () => {
  const tabPkgBtn = document.getElementById('tab-package-btn');
  const tabLockBtn = document.getElementById('tab-lockfile-btn');
  const tabPkgPane = document.getElementById('tab-package');
  const tabLockPane = document.getElementById('tab-lockfile');

  const pkgInput = document.getElementById('pkg-input');
  const pkgAuditBtn = document.getElementById('pkg-audit-btn');
  const lockfileInput = document.getElementById('lockfile-input');
  const lockfileAuditBtn = document.getElementById('lockfile-audit-btn');
  const lockfileExampleBtn = document.getElementById('lockfile-example-btn');
  const resultsArea = document.getElementById('audit-results');

  // Tab switching
  tabPkgBtn?.addEventListener('click', () => {
    tabPkgBtn.classList.add('active');
    tabPkgBtn.setAttribute('aria-selected', 'true');
    tabLockBtn.classList.remove('active');
    tabLockBtn.setAttribute('aria-selected', 'false');
    tabPkgPane.style.display = 'block';
    tabLockPane.style.display = 'none';
  });

  tabLockBtn?.addEventListener('click', () => {
    tabLockBtn.classList.add('active');
    tabLockBtn.setAttribute('aria-selected', 'true');
    tabPkgBtn.classList.remove('active');
    tabPkgBtn.setAttribute('aria-selected', 'false');
    tabLockPane.style.display = 'block';
    tabPkgPane.style.display = 'none';
  });

  // Presets
  document.querySelectorAll('.preset-pill').forEach(btn => {
    btn.addEventListener('click', () => {
      const pkg = btn.getAttribute('data-pkg');
      if (pkg && pkgInput) {
        pkgInput.value = pkg;
        runPackageAudit(pkg);
      }
    });
  });

  // Single Package Audit Action
  pkgAuditBtn?.addEventListener('click', () => {
    const pkg = pkgInput?.value.trim();
    if (pkg) {
      runPackageAudit(pkg);
    }
  });

  pkgInput?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      const pkg = pkgInput.value.trim();
      if (pkg) runPackageAudit(pkg);
    }
  });

  // Lockfile Audit Action
  lockfileAuditBtn?.addEventListener('click', () => {
    const content = lockfileInput?.value.trim();
    if (content) {
      runLockfileAudit(content);
    }
  });

  lockfileExampleBtn?.addEventListener('click', () => {
    if (lockfileInput) {
      lockfileInput.value = JSON.stringify({
        "require": {
          "paypal/paypal-server-sdk": "^2.4",
          "barryvdh/laravel-dompdf": "^3.1",
          "symfony/http-foundation": "^7.1"
        }
      }, null, 2);
    }
  });

  async function runPackageAudit(packageName) {
    setLoading(pkgAuditBtn, true);
    resultsArea.style.display = 'block';
    resultsArea.innerHTML = '<div class="audit-card">⏳ Resolving transitive dependencies from Packagist v2 API...</div>';

    try {
      const resp = await fetch('/api/composer-license-checker/audit-package', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ package: packageName })
      });
      const data = await resp.json();

      if (data.status === 'success') {
        renderPackageResult(data.result);
      } else {
        resultsArea.innerHTML = `<div class="audit-card error">❌ <strong>Audit Error:</strong> ${escapeHtml(data.message)}</div>`;
      }
    } catch (err) {
      resultsArea.innerHTML = `<div class="audit-card error">❌ <strong>Network Error:</strong> ${escapeHtml(err.message)}</div>`;
    } finally {
      setLoading(pkgAuditBtn, false);
    }
  }

  async function runLockfileAudit(content) {
    setLoading(lockfileAuditBtn, true);
    resultsArea.style.display = 'block';
    resultsArea.innerHTML = '<div class="audit-card">⏳ Auditing full dependency tree...</div>';

    try {
      const resp = await fetch('/api/composer-license-checker/audit-lockfile', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content: content })
      });
      const data = await resp.json();

      if (data.status === 'success') {
        renderLockfileResult(data.result);
      } else {
        resultsArea.innerHTML = `<div class="audit-card error">❌ <strong>Audit Error:</strong> ${escapeHtml(data.message)}</div>`;
      }
    } catch (err) {
      resultsArea.innerHTML = `<div class="audit-card error">❌ <strong>Network Error:</strong> ${escapeHtml(err.message)}</div>`;
    } finally {
      setLoading(lockfileAuditBtn, false);
    }
  }

  function renderPackageResult(res) {
    const requiresReview = res.requires_review;
    const cardClass = requiresReview ? 'review' : 'clean';
    const statusIcon = requiresReview ? '⚠️' : '✅';
    const declared = res.declared_licenses.join(', ') || 'Unspecified';

    let violationsHtml = '';
    if (res.violations && res.violations.length > 0) {
      violationsHtml = `
        <h4 style="margin-top: 1.25rem; color: #fca5a5;">Copyleft license signals (${res.violations.length}):</h4>
        ${res.violations.map(v => `
          <div class="trace-path-box">
            <div><strong>Dependency:</strong> <code>${escapeHtml(v.package_name)}@${escapeHtml(v.version)}</code> <span class="badge-tag ${v.is_strong_copyleft ? 'strong' : 'weak'}">${escapeHtml(v.license)}</span></div>
            <div style="margin-top: 0.35rem; color: #94a3b8;"><strong>Trace Path:</strong> ${escapeHtml(v.dependency_path)}</div>
            <div style="margin-top: 0.35rem; font-size: 0.8rem; color: #cbd5e1;"><em>${escapeHtml(v.description)}</em></div>
          </div>
        `).join('')}
      `;
    }

    resultsArea.innerHTML = `
      <div class="audit-card ${cardClass}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h3 style="margin: 0; font-size: 1.25rem;">${statusIcon} <code>${escapeHtml(res.package_name)}@${escapeHtml(res.version)}</code></h3>
          <span class="badge-tag ${requiresReview ? 'strong' : 'clean'}">${escapeHtml(res.verdict)}</span>
        </div>
        <p style="margin: 0 0 0.5rem; color: #cbd5e1;"><strong>Declared License:</strong> <code>${escapeHtml(declared)}</code> | <strong>Transitive Dependencies:</strong> ${res.total_dependencies}</p>
        <p style="margin: 0; color: #94a3b8;">${escapeHtml(res.summary)}</p>
        ${res.warnings?.length ? `<p style="margin-top:0.75rem;color:#fbbf24;"><strong>Incomplete:</strong> ${escapeHtml(res.warnings.join(' '))}</p>` : ''}
        ${violationsHtml}
      </div>
    `;
  }

  function renderLockfileResult(res) {
    const requiresReview = res.production_requires_review;
    const cardClass = requiresReview ? 'review' : 'clean';
    const statusIcon = requiresReview ? '⚠️' : '✅';

    let prodRows = res.production_packages.map(p => `
      <div style="padding: 0.6rem 0; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; justify-content: space-between; align-items: center;">
        <div>
          <code>${escapeHtml(p.package_name)}@${escapeHtml(p.version)}</code>
          <span style="color: #64748b; font-size: 0.85rem; margin-left: 0.5rem;">(${escapeHtml(p.declared_licenses.join(', ') || 'None')})</span>
        </div>
        <span class="badge-tag ${p.requires_review ? 'strong' : 'clean'}">${p.requires_review ? 'REVIEW ⚠️' : 'NO SIGNAL ✅'}</span>
      </div>
    `).join('');

    resultsArea.innerHTML = `
      <div class="audit-card ${cardClass}">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
          <h3 style="margin: 0;">${statusIcon} Project Dependency Audit Result</h3>
          <span class="badge-tag ${requiresReview ? 'strong' : 'clean'}">${escapeHtml(res.overall_verdict)}</span>
        </div>
        <p style="color: #cbd5e1;">
          <strong>Production Packages:</strong> ${res.total_prod_packages} (${res.review_prod_count} to review) |
          <strong>Dev Packages:</strong> ${res.total_dev_packages} (${res.review_dev_count} to review)
        </p>
        <p style="color:#94a3b8;"><strong>Mode:</strong> ${escapeHtml(res.audit_mode)} - ${escapeHtml(res.scope_note)}</p>
        <div style="margin-top: 1rem;">
          <h4 style="margin-bottom: 0.5rem;">Package Breakdown:</h4>
          ${prodRows}
        </div>
      </div>
    `;
  }

  function setLoading(btn, isLoading) {
    if (!btn) return;
    btn.disabled = isLoading;
    const spinner = btn.querySelector('.btn-spinner');
    const text = btn.querySelector('.btn-text');
    if (spinner) spinner.style.display = isLoading ? 'inline' : 'none';
    if (text) text.style.opacity = isLoading ? '0.5' : '1';
  }

  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
  }
});
