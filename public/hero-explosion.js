(function (root, factory) {
  const api = factory();

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = api;
  }

  if (root && root.document) {
    root.addEventListener('DOMContentLoaded', function () {
      api.initHeroExplosion(root.document, root);
    });
  }
}(typeof window !== 'undefined' ? window : null, function () {
  'use strict';

  function createBlastProfile(random) {
    return {
      fireEdge: Array.from({ length: 64 }, function () { return .78 + random() * .28; }),
      smokeEdge: Array.from({ length: 52 }, function () { return .72 + random() * .36; }),
      embers: Array.from({ length: 34 }, function () {
        return {
          angle: random() * Math.PI * 2,
          distance: .55 + random() * .65,
          length: 8 + random() * 24,
          delay: random() * .24
        };
      })
    };
  }

  function easeOutCubic(value) {
    return 1 - Math.pow(1 - value, 3);
  }

  function drawBlob(context, centerX, centerY, radiusX, radiusY, edge, wobble) {
    context.beginPath();
    edge.forEach(function (noise, index) {
      const angle = (index / edge.length) * Math.PI * 2;
      const pulse = 1 + Math.sin((angle * 5) + wobble) * .055;
      const x = centerX + Math.cos(angle) * radiusX * noise * pulse;
      const y = centerY + Math.sin(angle) * radiusY * noise * pulse;
      if (index === 0) {
        context.moveTo(x, y);
      } else {
        context.lineTo(x, y);
      }
    });
    context.closePath();
  }

  function drawShockwave(context, centerX, centerY, progress, maxRadius) {
    if (progress > .38) {
      return;
    }
    const localProgress = progress / .38;
    const radius = 45 + easeOutCubic(localProgress) * maxRadius;
    context.strokeStyle = 'rgba(255, 220, 122,' + ((1 - localProgress) * .75) + ')';
    context.lineWidth = 5 - (localProgress * 4);
    context.beginPath();
    context.ellipse(centerX, centerY, radius, radius * .48, 0, 0, Math.PI * 2);
    context.stroke();
  }

  function drawFireFront(context, profile, centerX, centerY, progress, width, height) {
    if (progress > .72) {
      return;
    }
    const localProgress = progress / .72;
    const expansion = easeOutCubic(localProgress);
    const fade = Math.sin(localProgress * Math.PI);
    const radiusX = 80 + expansion * Math.min(width * .47, 520);
    const radiusY = 52 + expansion * Math.min(height * .72, 260);
    const gradient = context.createRadialGradient(centerX, centerY, 0, centerX, centerY, radiusX);
    gradient.addColorStop(0, 'rgba(255,255,224,' + Math.min(1, fade * 1.35) + ')');
    gradient.addColorStop(.14, 'rgba(255,225,67,' + fade + ')');
    gradient.addColorStop(.42, 'rgba(255,105,20,' + (fade * .94) + ')');
    gradient.addColorStop(.72, 'rgba(214,35,8,' + (fade * .8) + ')');
    gradient.addColorStop(1, 'rgba(70,7,2,0)');
    context.save();
    context.globalCompositeOperation = 'lighter';
    context.shadowColor = 'rgba(255, 74, 16, .8)';
    context.shadowBlur = 32;
    context.fillStyle = gradient;
    drawBlob(context, centerX, centerY, radiusX, radiusY, profile.fireEdge, progress * 18);
    context.fill();
    context.restore();
  }

  function drawSmokeCloud(context, profile, centerX, centerY, progress, width, height) {
    if (progress < .28) {
      return;
    }
    const localProgress = (progress - .28) / .72;
    const expansion = easeOutCubic(localProgress);
    const fade = Math.sin(localProgress * Math.PI);
    const radiusX = 100 + expansion * Math.min(width * .44, 480);
    const radiusY = 48 + expansion * Math.min(height * .68, 245);
    const liftedY = centerY - (localProgress * 42);
    const gradient = context.createRadialGradient(centerX, liftedY, radiusX * .05, centerX, liftedY, radiusX);
    gradient.addColorStop(0, 'rgba(58,45,45,' + (fade * .84) + ')');
    gradient.addColorStop(.52, 'rgba(31,27,32,' + (fade * .72) + ')');
    gradient.addColorStop(1, 'rgba(9,10,15,0)');
    context.save();
    context.fillStyle = gradient;
    context.filter = 'blur(' + (3 + localProgress * 7) + 'px)';
    drawBlob(context, centerX, liftedY, radiusX, radiusY, profile.smokeEdge, progress * 11);
    context.fill();
    context.restore();
  }

  function drawEmbers(context, profile, centerX, centerY, progress, maxRadius) {
    profile.embers.forEach(function (ember) {
      const localProgress = Math.max(0, (progress - ember.delay) / (1 - ember.delay));
      if (localProgress <= 0 || localProgress >= 1) {
        return;
      }
      const distance = easeOutCubic(localProgress) * maxRadius * ember.distance;
      const x = centerX + Math.cos(ember.angle) * distance;
      const y = centerY + Math.sin(ember.angle) * distance * .52;
      const alpha = 1 - localProgress;
      context.strokeStyle = 'rgba(255,190,48,' + alpha + ')';
      context.lineWidth = 1.5;
      context.beginPath();
      context.moveTo(x, y);
      context.lineTo(
        x - Math.cos(ember.angle) * ember.length,
        y - Math.sin(ember.angle) * ember.length * .52
      );
      context.stroke();
    });
  }

  function initHeroExplosion(document, window) {
    const hero = document.querySelector('.toolbox-hero');
    const heading = hero ? hero.querySelector('[data-catops-heading]') : null;
    if (!heading) {
      return null;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    if (!context) {
      return null;
    }

    canvas.className = 'hero-explosion-canvas';
    canvas.setAttribute('aria-hidden', 'true');
    heading.classList.add('hero-explosion-target');
    heading.appendChild(canvas);

    let profile = null;
    let animationFrame = null;
    let explosionTimer = null;
    let explosionStart = 0;
    let ratio = 1;

    function sizeCanvas() {
      const width = heading.clientWidth + 300;
      const height = heading.clientHeight + 220;
      ratio = Math.min(window.devicePixelRatio || 1, 1.5);
      canvas.width = Math.round(width * ratio);
      canvas.height = Math.round(height * ratio);
      canvas.style.width = width + 'px';
      canvas.style.height = height + 'px';
      canvas.style.left = '-150px';
      canvas.style.top = '-110px';
      context.setTransform(ratio, 0, 0, ratio, 0, 0);
      return { width: width, height: height };
    }

    function attachCanvas() {
      if (!canvas.isConnected) {
        heading.appendChild(canvas);
      }
      sizeCanvas();
    }

    function render(timestamp) {
      const width = canvas.width / ratio;
      const height = canvas.height / ratio;
      const progress = Math.min(1, (timestamp - explosionStart) / 1450);
      const centerX = width / 2;
      const centerY = height / 2;
      const maxRadius = Math.min(width * .48, 540);
      context.clearRect(0, 0, width, height);
      drawShockwave(context, centerX, centerY, progress, maxRadius);
      drawFireFront(context, profile, centerX, centerY, progress, width, height);
      drawEmbers(context, profile, centerX, centerY, progress, maxRadius);
      drawSmokeCloud(context, profile, centerX, centerY, progress, width, height);

      if (progress < 1) {
        animationFrame = window.requestAnimationFrame(render);
      } else {
        context.clearRect(0, 0, width, height);
        heading.classList.remove('is-detonating');
        animationFrame = null;
      }
    }

    function explode() {
      if (reducedMotion || animationFrame !== null) {
        return;
      }
      sizeCanvas();
      profile = createBlastProfile(Math.random);
      explosionStart = window.performance.now();
      heading.classList.remove('is-detonating');
      void heading.offsetWidth;
      heading.classList.add('is-detonating');
      animationFrame = window.requestAnimationFrame(render);
    }

    function scheduleExplosion(delay) {
      if (reducedMotion) {
        return;
      }
      window.clearTimeout(explosionTimer);
      explosionTimer = window.setTimeout(function () {
        if (!document.hidden) {
          explode();
        }
        scheduleExplosion(8500 + Math.round(Math.random() * 3500));
      }, delay);
    }

    window.addEventListener('resize', function () {
      if (animationFrame === null) {
        sizeCanvas();
      }
    });
    window.addEventListener('stackhal:detonate', explode);
    window.addEventListener('stackhal:hero-copy-changed', attachCanvas);
    scheduleExplosion(1800);
    return { explode: explode };
  }

  return {
    createBlastProfile: createBlastProfile,
    initHeroExplosion: initHeroExplosion
  };
}));
