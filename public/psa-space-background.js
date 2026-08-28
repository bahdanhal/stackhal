(function (root, factory) {
  const api = factory();

  if (typeof module !== 'undefined' && module.exports) {
    module.exports = api;
  }

  if (root && root.document) {
    root.addEventListener('DOMContentLoaded', function () {
      api.initPsaSpaceBackground(root.document, root);
    });
  }
}(typeof window !== 'undefined' ? window : null, function () {
  'use strict';

  const PUBLIC_SAFETY_MESSAGES = [
    ['Ladybug tucked into bed,', 'keeps every cigarette in the shed.'],
    ['Love your children, hear them out,', 'hugs are what the world is about.'],
    ['Red light glowing, do not race,', 'everyone deserves a safer place.'],
    ['Click your seat belt before the ride,', 'keep your skeleton safe inside.'],
    ['Do not text while wheels rotate,', 'memes can scroll, but brakes cannot wait.'],
    ['Wash each finger, scrub each thumb,', 'tiny germs are rarely fun.'],
    ['Call your grandma, make her grin,', 'she may share her Wi-Fi PIN.'],
    ['Be kind online, resist the rage,', 'a person lives behind the page.'],
    ['Give the pigeon half your bread,', 'not the password in your head.']
  ];

  function hashString(value) {
    let hash = 2166136261;
    for (let index = 0; index < value.length; index += 1) {
      hash ^= value.charCodeAt(index);
      hash = Math.imul(hash, 16777619);
    }
    return hash >>> 0;
  }

  function createSeededRandom(seed) {
    let state = seed || 1;
    return function random() {
      state += 0x6D2B79F5;
      let value = state;
      value = Math.imul(value ^ (value >>> 15), value | 1);
      value ^= value + Math.imul(value ^ (value >>> 7), value | 61);
      return ((value ^ (value >>> 14)) >>> 0) / 4294967296;
    };
  }

  function shuffledIndexes(length, random) {
    const indexes = Array.from({ length: length }, function (_, index) { return index; });
    for (let index = indexes.length - 1; index > 0; index -= 1) {
      const target = Math.floor(random() * (index + 1));
      const current = indexes[index];
      indexes[index] = indexes[target];
      indexes[target] = current;
    }
    return indexes;
  }

  function roundedRectangle(context, x, y, width, height, radius) {
    const safeRadius = Math.min(radius, width / 2, height / 2);
    context.beginPath();
    context.moveTo(x + safeRadius, y);
    context.arcTo(x + width, y, x + width, y + height, safeRadius);
    context.arcTo(x + width, y + height, x, y + height, safeRadius);
    context.arcTo(x, y + height, x, y, safeRadius);
    context.arcTo(x, y, x + width, y, safeRadius);
    context.closePath();
  }

  function drawLadybug(context, x, y, size) {
    context.save();
    context.translate(x, y);
    context.fillStyle = 'rgba(255, 130, 150, .55)';
    context.beginPath();
    context.ellipse(0, 0, size * .55, size * .7, 0, 0, Math.PI * 2);
    context.fill();
    context.strokeStyle = 'rgba(184, 255, 90, .5)';
    context.lineWidth = 1;
    context.beginPath();
    context.moveTo(0, -size * .65);
    context.lineTo(0, size * .65);
    context.stroke();
    context.fillStyle = 'rgba(9, 10, 15, .85)';
    [[-.22, -.25], [.22, -.05], [-.2, .25], [.2, .38]].forEach(function (point) {
      context.beginPath();
      context.arc(point[0] * size, point[1] * size, size * .08, 0, Math.PI * 2);
      context.fill();
    });
    context.restore();
  }

  function drawSymbol(context, index, x, y, size) {
    context.save();
    context.translate(x, y);
    context.strokeStyle = 'rgba(184, 255, 90, .55)';
    context.fillStyle = 'rgba(120, 167, 255, .18)';
    context.lineWidth = 2;

    if (index % 4 === 0) {
      context.restore();
      drawLadybug(context, x, y, size);
      return;
    }

    if (index % 4 === 1) {
      context.beginPath();
      context.moveTo(0, size * .45);
      context.bezierCurveTo(-size, -size * .2, -size * .45, -size, 0, -size * .45);
      context.bezierCurveTo(size * .45, -size, size, -size * .2, 0, size * .45);
      context.fill();
      context.stroke();
    } else if (index % 4 === 2) {
      roundedRectangle(context, -size * .32, -size * .75, size * .64, size * 1.5, size * .16);
      context.stroke();
      ['#ff8296', '#ffcc66', '#7bf0c3'].forEach(function (color, lightIndex) {
        context.fillStyle = color;
        context.globalAlpha = .42;
        context.beginPath();
        context.arc(0, (-.45 + (lightIndex * .45)) * size, size * .13, 0, Math.PI * 2);
        context.fill();
      });
    } else {
      context.beginPath();
      context.moveTo(-size * .7, size * .55);
      context.lineTo(size * .7, -size * .55);
      context.moveTo(-size * .58, -size * .6);
      context.lineTo(size * .58, size * .6);
      context.stroke();
      context.fillRect(-size * .72, size * .42, size * .3, size * .28);
      context.fillRect(size * .42, -size * .7, size * .3, size * .28);
    }
    context.restore();
  }

  function drawPoster(context, poster, message, index) {
    context.save();
    context.translate(poster.x, poster.y);
    context.rotate(poster.rotation);
    roundedRectangle(context, -poster.width / 2, -poster.height / 2, poster.width, poster.height, 7);
    context.fillStyle = 'rgba(13, 15, 24, .48)';
    context.fill();
    context.strokeStyle = index % 2 === 0 ? 'rgba(184, 255, 90, .28)' : 'rgba(120, 167, 255, .3)';
    context.lineWidth = 1;
    context.stroke();

    drawSymbol(context, index, 0, -poster.height * .19, Math.min(25, poster.width * .12));
    context.fillStyle = 'rgba(221, 226, 245, .44)';
    context.font = '700 ' + Math.max(9, Math.min(12, poster.width / 20)) + 'px ui-monospace, monospace';
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.fillText(message[0].toUpperCase(), 0, poster.height * .17, poster.width - 22);
    context.fillText(message[1].toUpperCase(), 0, poster.height * .27, poster.width - 22);
    context.restore();
  }

  function drawRocket(context, rocket, time, width, height) {
    const progress = ((time * rocket.speed) + rocket.offset) % 1.35 - .18;
    const x = progress * width;
    const y = rocket.y * height + Math.sin((progress + rocket.offset) * 8) * 22;
    context.save();
    context.translate(x, y);
    context.rotate(-.18 + Math.sin(time * .001) * .04);
    context.strokeStyle = 'rgba(184, 255, 90, .42)';
    context.fillStyle = 'rgba(120, 167, 255, .16)';
    context.lineWidth = 1.5;
    context.beginPath();
    context.moveTo(20, 0);
    context.lineTo(-7, -8);
    context.lineTo(-14, 0);
    context.lineTo(-7, 8);
    context.closePath();
    context.fill();
    context.stroke();
    context.beginPath();
    context.moveTo(-14, 0);
    context.lineTo(-35 - Math.sin(time * .02) * 8, 0);
    context.strokeStyle = 'rgba(255, 204, 102, .34)';
    context.stroke();
    context.restore();
  }

  function drawComet(context, comet, time, width, height) {
    const progress = ((time * comet.speed) + comet.offset) % 1.4 - .2;
    const x = width - (progress * width);
    const y = (comet.y * height) + (progress * height * .34);
    const gradient = context.createLinearGradient(x, y, x + 90, y - 32);
    gradient.addColorStop(0, 'rgba(201, 133, 255, .5)');
    gradient.addColorStop(1, 'rgba(201, 133, 255, 0)');
    context.strokeStyle = gradient;
    context.lineWidth = 2;
    context.beginPath();
    context.moveTo(x, y);
    context.lineTo(x + 90, y - 32);
    context.stroke();
    context.fillStyle = 'rgba(243, 245, 255, .65)';
    context.beginPath();
    context.arc(x, y, 3, 0, Math.PI * 2);
    context.fill();
  }

  function drawPlanet(context, planet, time) {
    context.save();
    context.translate(planet.x, planet.y);
    context.rotate(planet.rotation + (time * planet.spin));
    context.strokeStyle = planet.ringColor;
    context.lineWidth = 1.2;
    context.beginPath();
    context.ellipse(0, 0, planet.radius * 1.8, planet.radius * .38, -.18, 0, Math.PI * 2);
    context.stroke();

    const gradient = context.createRadialGradient(
      -planet.radius * .3,
      -planet.radius * .35,
      planet.radius * .12,
      0,
      0,
      planet.radius
    );
    gradient.addColorStop(0, planet.highlightColor);
    gradient.addColorStop(1, 'rgba(13, 15, 24, .12)');
    context.fillStyle = gradient;
    context.beginPath();
    context.arc(0, 0, planet.radius, 0, Math.PI * 2);
    context.fill();
    context.strokeStyle = planet.edgeColor;
    context.stroke();

    planet.craters.forEach(function (crater) {
      context.fillStyle = 'rgba(9, 10, 15, .2)';
      context.beginPath();
      context.ellipse(
        crater.x * planet.radius,
        crater.y * planet.radius,
        crater.size * planet.radius,
        crater.size * planet.radius * .65,
        crater.rotation,
        0,
        Math.PI * 2
      );
      context.fill();
    });
    context.restore();
  }

  function drawConstellation(context, constellation) {
    context.strokeStyle = constellation.color;
    context.fillStyle = constellation.color;
    context.lineWidth = .8;
    context.beginPath();
    constellation.points.forEach(function (point, index) {
      if (index === 0) {
        context.moveTo(point.x, point.y);
      } else {
        context.lineTo(point.x, point.y);
      }
    });
    context.stroke();
    constellation.points.forEach(function (point, index) {
      context.beginPath();
      context.arc(point.x, point.y, index % 3 === 0 ? 2.1 : 1.2, 0, Math.PI * 2);
      context.fill();
    });
  }

  function initPsaSpaceBackground(document, window) {
    const canvas = document.querySelector('.psa-space-background');
    if (!canvas) {
      return null;
    }

    const context = canvas.getContext('2d');
    if (!context) {
      return null;
    }

    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const random = createSeededRandom(hashString(window.location.pathname));
    let width = 0;
    let height = 0;
    let ratio = 1;
    let posters = [];
    let stars = [];
    let planet = null;
    let constellation = null;
    const palette = [
      ['rgba(184, 255, 90, .28)', 'rgba(184, 255, 90, .2)', 'rgba(120, 167, 255, .28)'],
      ['rgba(201, 133, 255, .3)', 'rgba(201, 133, 255, .2)', 'rgba(255, 204, 102, .28)'],
      ['rgba(120, 167, 255, .3)', 'rgba(120, 167, 255, .19)', 'rgba(123, 240, 195, .28)'],
      ['rgba(255, 204, 102, .3)', 'rgba(255, 204, 102, .18)', 'rgba(255, 130, 150, .25)']
    ][hashString(window.location.pathname) % 4];
    const rockets = [{ y: .22, speed: .000035, offset: random() }, { y: .72, speed: .000024, offset: random() }];
    const comets = [{ y: .08, speed: .000029, offset: random() }, { y: .48, speed: .000019, offset: random() }];

    function resize() {
      ratio = Math.min(window.devicePixelRatio || 1, 1.5);
      width = window.innerWidth;
      height = window.innerHeight;
      canvas.width = Math.round(width * ratio);
      canvas.height = Math.round(height * ratio);
      canvas.style.width = width + 'px';
      canvas.style.height = height + 'px';
      context.setTransform(ratio, 0, 0, ratio, 0, 0);

      const posterCount = width < 800 ? 3 : 6;
      const messageOrder = shuffledIndexes(PUBLIC_SAFETY_MESSAGES.length, random);
      posters = Array.from({ length: posterCount }, function (_, index) {
        const onLeft = index % 2 === 0;
        return {
          x: onLeft ? width * (.04 + random() * .16) : width * (.80 + random() * .16),
          y: height * (.12 + random() * .78),
          width: Math.min(225, Math.max(150, width * .14)),
          height: 132 + random() * 32,
          rotation: (random() - .5) * .2,
          messageIndex: messageOrder[index]
        };
      });
      stars = Array.from({ length: width < 800 ? 28 : 65 }, function () {
        return { x: random() * width, y: random() * height, size: .4 + random() * 1.5, alpha: .08 + random() * .24 };
      });
      planet = {
        x: random() > .5 ? width * (.02 + random() * .12) : width * (.86 + random() * .12),
        y: height * (.16 + random() * .68),
        radius: width < 800 ? 48 + random() * 26 : 72 + random() * 55,
        rotation: random() * Math.PI,
        spin: (random() - .5) * .000008,
        highlightColor: palette[1],
        edgeColor: palette[0],
        ringColor: palette[2],
        craters: Array.from({ length: 4 }, function () {
          return {
            x: random() * .9 - .45,
            y: random() * .9 - .45,
            size: .05 + random() * .12,
            rotation: random() * Math.PI
          };
        })
      };
      const constellationOrigin = {
        x: width * (.22 + random() * .5),
        y: height * (.08 + random() * .22)
      };
      constellation = {
        color: palette[0],
        points: Array.from({ length: 7 }, function (_, index) {
          return {
            x: constellationOrigin.x + ((index - 3) * (30 + random() * 22)),
            y: constellationOrigin.y + ((random() - .5) * 75)
          };
        })
      };
    }

    function draw(time) {
      context.clearRect(0, 0, width, height);
      stars.forEach(function (star) {
        context.fillStyle = 'rgba(243, 245, 255,' + star.alpha + ')';
        context.fillRect(star.x, star.y, star.size, star.size);
      });
      drawConstellation(context, constellation);
      drawPlanet(context, planet, time);
      posters.forEach(function (poster, index) {
        drawPoster(context, poster, PUBLIC_SAFETY_MESSAGES[poster.messageIndex], index);
      });
      rockets.forEach(function (rocket) { drawRocket(context, rocket, time, width, height); });
      comets.forEach(function (comet) { drawComet(context, comet, time, width, height); });

      if (!reducedMotion) {
        window.requestAnimationFrame(draw);
      }
    }

    resize();
    window.addEventListener('resize', resize);
    window.requestAnimationFrame(draw);
    return { redraw: draw, resize: resize };
  }

  return {
    PUBLIC_SAFETY_MESSAGES: PUBLIC_SAFETY_MESSAGES,
    createSeededRandom: createSeededRandom,
    hashString: hashString,
    initPsaSpaceBackground: initPsaSpaceBackground,
    shuffledIndexes: shuffledIndexes
  };
}));
