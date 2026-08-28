(function (root, factory) {
  const api = factory();

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = api;
  }

  if (root && root.document) {
    root.addEventListener('DOMContentLoaded', function () {
      api.initCatOps(root.document, root);
    });
  }
}(typeof window !== 'undefined' ? window : null, function () {
  'use strict';

  function createActivationGate(requiredClicks, windowMs) {
    let clicks = [];

    return function registerClick(timestamp) {
      clicks = clicks.filter(function (click) {
        return timestamp - click <= windowMs;
      });
      clicks.push(timestamp);

      if (clicks.length < requiredClicks) {
        return false;
      }

      clicks = [];
      return true;
    };
  }

  function pointDistance(first, second) {
    return Math.hypot(first.x - second.x, first.y - second.y);
  }

  function initCatOps(document, window) {
    const hero = document.querySelector('.toolbox-hero');
    if (!hero) {
      return null;
    }

    const orbit = hero.querySelector('.toolbox-orbit');
    const heading = hero.querySelector('[data-catops-heading]');
    const eyebrow = hero.querySelector('.eyebrow');
    const scene = hero.querySelector('.catops-scene');
    const cat = hero.querySelector('.catops-cat');
    const mouse = hero.querySelector('.catops-mouse');
    const toast = hero.querySelector('.catops-toast');
    const statusItems = Array.from(hero.querySelectorAll('.toolbox-status li'));
    const triggers = Array.from(hero.querySelectorAll('.catops-trigger, [data-catops-mobile-trigger]'));

    if (!orbit || !heading || !eyebrow || !scene || !cat || !mouse || !toast || triggers.length === 0) {
      return null;
    }

    const original = {
      heading: heading.textContent,
      eyebrow: eyebrow.textContent,
      statuses: statusItems.map(function (item) { return item.textContent; })
    };
    const runningStatuses = hero.dataset.catopsRunningStatus.split('||');
    const resolvedStatuses = hero.dataset.catopsResolvedStatus.split('||');
    const activationGate = createActivationGate(3, 900);
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const coarsePointer = window.matchMedia('(pointer: coarse)').matches;
    let running = false;
    let animationFrame = null;
    let restoreTimer = null;
    let startedAt = 0;
    let lastPointerMove = 0;
    let direction = 1;
    let catPoint = { x: 0, y: 0 };
    let mousePoint = { x: window.innerWidth / 2, y: window.innerHeight / 2 };

    function setStatuses(values) {
      statusItems.forEach(function (item, index) {
        item.textContent = values[index] || original.statuses[index];
      });
    }

    function orbitCenter() {
      const bounds = orbit.getBoundingClientRect();
      return {
        x: bounds.left + (bounds.width / 2),
        y: bounds.top + (bounds.height / 2)
      };
    }

    function positionCat(point) {
      cat.style.transform = 'translate3d(' + (point.x - 45) + 'px,' + (point.y - 38) + 'px,0) scaleX(' + direction + ')';
    }

    function positionMouse(point) {
      mouse.style.transform = 'translate3d(' + (point.x + 14) + 'px,' + (point.y + 10) + 'px,0)';
    }

    function restoreCopy() {
      heading.textContent = original.heading;
      window.dispatchEvent(new window.CustomEvent('stackhal:hero-copy-changed'));
      eyebrow.textContent = original.eyebrow;
      setStatuses(original.statuses);
      hero.classList.remove('catops-incident');
      toast.hidden = true;
    }

    function parkCat() {
      const center = orbitCenter();
      direction = 1;
      catPoint = center;
      positionCat(center);
      mouse.hidden = true;
      scene.classList.remove('catops-running');
    }

    function resolveIncident() {
      if (!running) {
        return;
      }

      running = false;
      if (animationFrame !== null) {
        window.cancelAnimationFrame(animationFrame);
      }
      animationFrame = null;
      setStatuses(resolvedStatuses);
      toast.textContent = hero.dataset.catopsResolved;
      parkCat();
      restoreTimer = window.setTimeout(restoreCopy, 4800);
    }

    function animate(timestamp) {
      if (!running) {
        return;
      }

      const elapsed = timestamp - startedAt;
      if (coarsePointer) {
        const center = { x: window.innerWidth / 2, y: window.innerHeight / 2 };
        mousePoint = {
          x: center.x + Math.cos(elapsed / 470) * Math.min(150, window.innerWidth * .3),
          y: center.y + Math.sin(elapsed / 330) * 95
        };
      }

      const previousX = catPoint.x;
      catPoint.x += (mousePoint.x - catPoint.x) * .075;
      catPoint.y += (mousePoint.y - catPoint.y) * .075;
      if (Math.abs(catPoint.x - previousX) > .4) {
        direction = catPoint.x < mousePoint.x ? 1 : -1;
      }
      positionCat(catPoint);
      positionMouse(mousePoint);

      const pointerIsResting = timestamp - lastPointerMove > 700;
      const closeEnough = pointDistance(catPoint, mousePoint) < 48;
      if ((elapsed > 5800 && pointerIsResting && closeEnough) || elapsed > 10000) {
        resolveIncident();
        return;
      }

      animationFrame = window.requestAnimationFrame(animate);
    }

    function activate() {
      if (running) {
        return;
      }

      window.clearTimeout(restoreTimer);
      running = true;
      hero.classList.add('catops-incident');
      heading.textContent = hero.dataset.catopsHeading;
      window.dispatchEvent(new window.CustomEvent('stackhal:hero-copy-changed'));
      eyebrow.textContent = hero.dataset.catopsEyebrow;
      setStatuses(runningStatuses);
      toast.textContent = hero.dataset.catopsAlert;
      toast.hidden = false;
      scene.hidden = false;
      scene.setAttribute('aria-hidden', 'false');
      scene.classList.add('catops-running');
      mouse.hidden = false;
      catPoint = orbitCenter();
      mousePoint = { x: window.innerWidth * .72, y: window.innerHeight * .55 };
      lastPointerMove = window.performance.now();
      startedAt = lastPointerMove;

      if (reducedMotion) {
        resolveIncident();
        return;
      }

      animationFrame = window.requestAnimationFrame(animate);
    }

    function cancel() {
      running = false;
      window.clearTimeout(restoreTimer);
      if (animationFrame !== null) {
        window.cancelAnimationFrame(animationFrame);
      }
      animationFrame = null;
      scene.hidden = true;
      scene.setAttribute('aria-hidden', 'true');
      restoreCopy();
    }

    triggers.forEach(function (trigger) {
      trigger.addEventListener('click', function () {
        if (activationGate(window.performance.now())) {
          activate();
        }
      });
    });

    window.addEventListener('pointermove', function (event) {
      if (!running || coarsePointer) {
        return;
      }
      mousePoint = { x: event.clientX, y: event.clientY };
      lastPointerMove = window.performance.now();
    });

    window.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && running) {
        cancel();
      }
    });

    window.addEventListener('resize', function () {
      if (!running && !scene.hidden) {
        parkCat();
      }
    });

    return { activate: activate, cancel: cancel };
  }

  return {
    createActivationGate: createActivationGate,
    initCatOps: initCatOps,
    pointDistance: pointDistance
  };
}));
