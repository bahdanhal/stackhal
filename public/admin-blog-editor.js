(function () {
  'use strict';

  var contentInput = document.getElementById('input-content');
  var titleInput = document.getElementById('input-title');
  var descInput = document.getElementById('input-description');
  var previewTarget = document.getElementById('preview-target');
  var btnClean = document.getElementById('btn-clean-symbols');

  var scoreFlesch = document.getElementById('score-flesch');
  var scoreGrade = document.getElementById('score-grade');
  var scoreWps = document.getElementById('score-wps');
  var scoreWords = document.getElementById('score-words');
  var scoreSymbols = document.getElementById('score-symbols');

  if (!contentInput || !previewTarget) {
    return;
  }

  var smartCharPatterns = [
    { regex: /\u2014/g, rep: '-', name: 'Em Dash' },
    { regex: /\u2013/g, rep: '-', name: 'En Dash' },
    { regex: /\u2018|\u2019/g, rep: "'", name: 'Curly Single Quote' },
    { regex: /\u201C|\u201D/g, rep: '"', name: 'Curly Double Quote' },
    { regex: /\u00A0/g, rep: ' ', name: 'Non-Breaking Space' },
  ];

  function countSyllables(word) {
    word = word.toLowerCase().replace(/[^a-z]/g, '');
    if (!word) return 0;
    if (word.length <= 3) return 1;
    word = word.replace(/(?:[^laeiouy]|ed|es|e)$/, '');
    word = word.replace(/^y/, '');
    var matches = word.match(/[aeiouy]{1,2}/g);
    return matches ? Math.max(1, matches.length) : 1;
  }

  function updateAnalysis() {
    var rawText = (titleInput ? titleInput.value : '') + ' '
                + (descInput ? descInput.value : '') + ' '
                + contentInput.value;

    // Check for smart characters
    var symbolCount = 0;
    for (var i = 0; i < smartCharPatterns.length; i++) {
      var found = rawText.match(smartCharPatterns[i].regex);
      if (found) {
        symbolCount += found.length;
      }
    }

    if (symbolCount === 0) {
      scoreSymbols.textContent = '0 (Clean)';
      scoreSymbols.className = 'score-val score-good';
    } else {
      scoreSymbols.textContent = symbolCount + ' AI symbol' + (symbolCount > 1 ? 's' : '') + ' found';
      scoreSymbols.className = 'score-val score-bad';
    }

    // Strip HTML and pre/code blocks for readability score
    var cleanText = contentInput.value
      .replace(/<pre[\s\S]*?<\/pre>/gi, '')
      .replace(/<code[\s\S]*?<\/code>/gi, '')
      .replace(/<[^>]+>/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();

    var sentences = cleanText.split(/[.!?]+/).filter(function (s) { return s.trim().length > 0; });
    var words = cleanText.match(/\b[A-Za-z0-9'-]+\b/g) || [];

    var numSentences = Math.max(1, sentences.length);
    var numWords = Math.max(1, words.length);

    var syllables = 0;
    for (var j = 0; j < words.length; j++) {
      syllables += countSyllables(words[j]);
    }

    var wps = numWords / numSentences;
    var spw = syllables / numWords;

    var flesch = 206.835 - (1.015 * wps) - (84.6 * spw);
    var grade = (0.39 * wps) + (11.8 * spw) - 15.59;

    scoreWords.textContent = words.length.toString();
    scoreWps.textContent = wps.toFixed(1);
    scoreGrade.textContent = Math.max(1, grade).toFixed(1);

    var roundedFlesch = Math.min(100, Math.max(0, flesch)).toFixed(1);
    scoreFlesch.textContent = roundedFlesch;

    if (flesch >= 65) {
      scoreFlesch.className = 'score-val score-good';
    } else if (flesch >= 50) {
      scoreFlesch.className = 'score-val score-warn';
    } else {
      scoreFlesch.className = 'score-val score-bad';
    }

    // Render Preview
    previewTarget.innerHTML = contentInput.value;
  }

  function cleanAllFields() {
    function clean(str) {
      var res = str;
      for (var i = 0; i < smartCharPatterns.length; i++) {
        res = res.replace(smartCharPatterns[i].regex, smartCharPatterns[i].rep);
      }
      return res;
    }

    if (titleInput) titleInput.value = clean(titleInput.value);
    if (descInput) descInput.value = clean(descInput.value);
    contentInput.value = clean(contentInput.value);

    updateAnalysis();
  }

  if (btnClean) {
    btnClean.addEventListener('click', cleanAllFields);
  }

  contentInput.addEventListener('input', updateAnalysis);
  if (titleInput) titleInput.addEventListener('input', updateAnalysis);
  if (descInput) descInput.addEventListener('input', updateAnalysis);

  // Toolbar buttons
  var toolbarButtons = document.querySelectorAll('.editor-toolbar button[data-tag]');
  toolbarButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var tag = btn.getAttribute('data-tag');
      var start = contentInput.selectionStart;
      var end = contentInput.selectionEnd;
      var selected = contentInput.value.substring(start, end);
      var replacement = '';

      switch (tag) {
        case 'h2':
          replacement = '<h2>' + (selected || 'Heading 2') + '</h2>';
          break;
        case 'h3':
          replacement = '<h3>' + (selected || 'Heading 3') + '</h3>';
          break;
        case 'bold':
          replacement = '<strong>' + (selected || 'Bold Text') + '</strong>';
          break;
        case 'code':
          replacement = '<code>' + (selected || 'code') + '</code>';
          break;
        case 'pre':
          replacement = '<pre class="article-code"><code>' + (selected || '# command or code\n') + '</code></pre>';
          break;
        case 'callout':
          replacement = '<div class="article-callout article-callout-accent"><strong>Key Finding</strong><span>' + (selected || 'Important takeaway or insight.') + '</span></div>';
          break;
        case 'list':
          replacement = '<ul class="article-checklist">\n  <li><strong>Item 1:</strong> Description</li>\n  <li><strong>Item 2:</strong> Description</li>\n</ul>';
          break;
      }

      contentInput.setRangeText(replacement, start, end, 'end');
      contentInput.focus();
      updateAnalysis();
    });
  });

  // Initial update
  updateAnalysis();
})();
