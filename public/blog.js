document.addEventListener('click', async event => {
  const button = event.target.closest('[data-copy-target]');
  if (!button) return;
  const target = document.getElementById(button.dataset.copyTarget || '');
  if (!target) return;

  const originalLabel = button.textContent;
  try {
    await navigator.clipboard.writeText(target.textContent.trim());
    button.textContent = 'Copied';
  } catch {
    button.textContent = 'Copy failed';
  }
  window.setTimeout(() => { button.textContent = originalLabel; }, 1800);
});
