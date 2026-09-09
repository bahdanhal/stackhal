const blogSearch = document.getElementById('blog-search');
if (blogSearch) {
  const searchWrapper = document.querySelector('[data-blog-search]');
  const entries = Array.from(document.querySelectorAll('[data-blog-entry]'));
  const searchableEntries = entries.map(entry => ({
    entry,
    text: entry.textContent.toLowerCase().replace(/\s+/g, ' ')
  }));
  const emptyMessage = document.querySelector('[data-blog-empty]');
  const status = document.querySelector('[data-blog-status]');

  if (searchWrapper) {
    searchWrapper.hidden = entries.length === 0;
  }

  blogSearch.addEventListener('input', () => {
    const words = blogSearch.value.toLowerCase().trim().split(/\s+/).filter(Boolean);
    let visibleCount = 0;

    searchableEntries.forEach(({ entry, text }) => {
      const match = words.every(word => text.includes(word));
      entry.hidden = !match;
      if (match) visibleCount += 1;
    });

    if (emptyMessage) {
      emptyMessage.hidden = visibleCount > 0 || entries.length === 0;
    }
    if (status) {
      status.textContent = `${visibleCount} ${visibleCount === 1 ? 'note' : 'notes'} found.`;
    }
  });
}

const sectionNavigation = document.querySelector('[data-article-sections]');
if (sectionNavigation) {
  const tocNav = sectionNavigation.closest('.article-toc');
  const headings = Array.from(document.querySelectorAll('#article-content h2'));

  if (headings.length === 0) {
    if (tocNav) tocNav.hidden = true;
  } else {
    headings.forEach((heading, index) => {
      if (!heading.id) {
        let slug = heading.textContent.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        let id = slug || `note-section-${index + 1}`;
        let counter = 1;
        while (document.getElementById(id)) {
          id = `${slug}-${counter++}`;
        }
        heading.id = id;
      }
      const link = document.createElement('a');
      link.href = `#${encodeURIComponent(heading.id)}`;
      link.textContent = heading.textContent;
      link.dataset.tocTarget = heading.id;
      sectionNavigation.appendChild(link);
    });

    if ('IntersectionObserver' in window) {
      const links = Array.from(sectionNavigation.querySelectorAll('a[data-toc-target]'));
      const observer = new IntersectionObserver((observedEntries) => {
        observedEntries.forEach(entry => {
          if (entry.isIntersecting) {
            links.forEach(l => l.classList.toggle('active', l.dataset.tocTarget === entry.target.id));
          }
        });
      }, { rootMargin: '0px 0px -65% 0px' });

      headings.forEach(h => observer.observe(h));
    }
  }
}

document.addEventListener('click', async event => {
  const button = event.target.closest('[data-copy-target]');
  if (!button) return;

  const targetSelector = button.getAttribute('data-copy-target');
  const targetElement = targetSelector ? document.querySelector(targetSelector) : button.closest('pre, .article-code');
  if (!targetElement) return;

  const textToCopy = targetElement.textContent.replace(/^Copy\s*/i, '').trim();
  const originalLabel = button.textContent;

  try {
    if (navigator.clipboard && window.isSecureContext) {
      await navigator.clipboard.writeText(textToCopy);
    } else {
      const textarea = document.createElement('textarea');
      textarea.value = textToCopy;
      textarea.style.position = 'fixed';
      textarea.style.opacity = '0';
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand('copy');
      document.body.removeChild(textarea);
    }
    button.textContent = 'Copied!';
    button.classList.add('copied');
    setTimeout(() => {
      button.textContent = originalLabel;
      button.classList.remove('copied');
    }, 2000);
  } catch {
    button.textContent = 'Failed to copy';
    setTimeout(() => {
      button.textContent = originalLabel;
    }, 2000);
  }
});
