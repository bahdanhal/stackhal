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
    context.fillStyle = 'rgba(144, 69, 58, .58)';
    context.beginPath();
    context.ellipse(0, 0, size * .55, size * .7, 0, 0, Math.PI * 2);
    context.fill();
    context.strokeStyle = 'rgba(190, 190, 182, .42)';
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
    context.strokeStyle = 'rgba(190, 190, 182, .5)';
    context.fillStyle = 'rgba(156, 158, 155, .12)';
    context.lineWidth = 1.2;

    if (index % 4 === 0) {
      context.restore();
      drawLadybug(context, x, y, size);
      return;
    }

    if (index % 4 === 1) {
      context.beginPath();
      context.arc(-size * .28, 0, size * .34, 0, Math.PI * 2);
      context.moveTo(size * .62, 0);
      context.arc(size * .28, 0, size * .34, 0, Math.PI * 2);
      context.stroke();
    } else if (index % 4 === 2) {
      roundedRectangle(context, -size * .32, -size * .75, size * .64, size * 1.5, size * .16);
      context.stroke();
      ['#685d57', '#9a8660', '#68756b'].forEach(function (color, lightIndex) {
        context.fillStyle = color;
        context.globalAlpha = .52;
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
    context.beginPath();
    context.rect(-poster.width / 2, -poster.height / 2, poster.width, poster.height);
    context.fillStyle = 'rgba(9, 10, 13, .68)';
    context.fill();
    context.strokeStyle = 'rgba(182, 183, 178, .25)';
    context.lineWidth = 1;
    context.stroke();

    context.fillStyle = 'rgba(188, 189, 184, .34)';
    context.font = '500 8px ui-monospace, monospace';
    context.textAlign = 'left';
    context.fillText('PUBLIC NOTICE / ' + String(index + 1).padStart(2, '0'), -poster.width / 2 + 10, -poster.height / 2 + 15);
    context.beginPath();
    context.moveTo(-poster.width / 2 + 10, -poster.height / 2 + 22);
    context.lineTo(poster.width / 2 - 10, -poster.height / 2 + 22);
    context.stroke();

    drawSymbol(context, index, 0, -poster.height * .14, Math.min(19, poster.width * .09));
    context.fillStyle = 'rgba(211, 212, 207, .46)';
    context.font = '600 ' + Math.max(8, Math.min(10, poster.width / 22)) + 'px ui-monospace, monospace';
    context.textAlign = 'center';
    context.textBaseline = 'middle';
    context.fillText(message[0].toUpperCase(), 0, poster.height * .19, poster.width - 22);
    context.fillText(message[1].toUpperCase(), 0, poster.height * .29, poster.width - 22);
    context.restore();
  }

  function drawRocket(context, rocket, time, width, height) {
    const progress = ((time * rocket.speed) + rocket.offset) % 1.35 - .18;
    const x = progress * width;
    const y = rocket.y * height + Math.sin((progress + rocket.offset) * 8) * 22;
    context.save();
    context.translate(x, y);
    context.rotate(-.18 + Math.sin(time * .001) * .04);
    context.strokeStyle = 'rgba(190, 192, 190, .38)';
    context.fillStyle = 'rgba(122, 128, 132, .1)';
    context.lineWidth = 1;
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
    context.strokeStyle = 'rgba(178, 143, 98, .26)';
    context.stroke();
    context.restore();
  }

  function drawComet(context, comet, time, width, height) {
    const progress = ((time * comet.speed) + comet.offset) % 1.4 - .2;
    const x = width - (progress * width);
    const y = (comet.y * height) + (progress * height * .34);
    const gradient = context.createLinearGradient(x, y, x + 90, y - 32);
    gradient.addColorStop(0, 'rgba(196, 188, 170, .38)');
    gradient.addColorStop(1, 'rgba(196, 188, 170, 0)');
    context.strokeStyle = gradient;
    context.lineWidth = 2;
    context.beginPath();
    context.moveTo(x, y);
    context.lineTo(x + 90, y - 32);
    context.stroke();
    context.fillStyle = 'rgba(224, 223, 216, .55)';
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
      ['rgba(157, 164, 157, .25)', 'rgba(119, 126, 120, .15)', 'rgba(151, 158, 164, .22)'],
      ['rgba(150, 143, 135, .25)', 'rgba(116, 108, 102, .15)', 'rgba(168, 150, 117, .2)'],
      ['rgba(135, 150, 159, .24)', 'rgba(104, 119, 126, .14)', 'rgba(126, 147, 139, .2)'],
      ['rgba(169, 151, 118, .23)', 'rgba(128, 113, 89, .14)', 'rgba(152, 128, 121, .19)']
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
