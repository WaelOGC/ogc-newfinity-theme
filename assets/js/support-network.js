/**
 * OGC NewFinity — Help & Support hero "support network" (2D canvas).
 * Central glowing "?" with loosely orbiting nodes and pulsed link lines.
 *
 * Drop-in: template-parts/hero-support-network.php (or any [data-ogc-support-network] root).
 */
(function () {
	'use strict';

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	function ogcReducedMotion() {
		return rmMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}

	/* Brand accents already used across other hero canvases. */
	var AMBER = '#FFB000';
	var AMBER_SOFT = '255, 210, 122';
	var AMBER_PALE = '255, 232, 179';

	var NODE_COUNT = 8;

	function initNetwork(root) {
		var canvas = root.querySelector('.ogc-support-network__canvas');
		if (!canvas) {
			return;
		}

		var ctx = canvas.getContext('2d');
		if (!ctx) {
			return;
		}

		var nodes = [];
		var cssW = 0;
		var cssH = 0;
		var cx = 0;
		var cy = 0;
		var scale = 1;
		var inView = true;
		var animId = 0;
		var lastTs = 0;
		var pulseT = 0;

		function stop() {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = 0;
			}
			lastTs = 0;
		}

		function seedNodes() {
			nodes = [];
			var i;
			for (i = 0; i < NODE_COUNT; i++) {
				var ring = i % 3;
				var baseR = 0.28 + ring * 0.11 + (i % 2) * 0.03;
				nodes.push({
					angle: (Math.PI * 2 * i) / NODE_COUNT + i * 0.17,
					radius: baseR,
					/* Radians per second — slow ambient drift. */
					speed: 0.045 + (i % 4) * 0.018 + (ring === 1 ? 0.012 : 0),
					dir: i % 2 === 0 ? 1 : -1,
					size: 2.2 + (i % 3) * 0.55,
					phase: i * 0.85
				});
			}
		}

		function resize() {
			var w = root.clientWidth || 320;
			var h = root.clientHeight || w;
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			cssW = w;
			cssH = h;
			cx = w * 0.5;
			cy = h * 0.5;
			scale = Math.min(w, h);
			canvas.width = Math.max(1, Math.floor(w * dpr));
			canvas.height = Math.max(1, Math.floor(h * dpr));
			canvas.style.width = w + 'px';
			canvas.style.height = h + 'px';
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
		}

		function nodePos(node) {
			var r = node.radius * scale * 0.5;
			return {
				x: cx + Math.cos(node.angle) * r,
				y: cy + Math.sin(node.angle) * r
			};
		}

		function nearestNeighbors(positions, index, count) {
			var list = [];
			var i;
			for (i = 0; i < positions.length; i++) {
				if (i === index) {
					continue;
				}
				var dx = positions[i].x - positions[index].x;
				var dy = positions[i].y - positions[index].y;
				list.push({ i: i, d2: dx * dx + dy * dy });
			}
			list.sort(function (a, b) {
				return a.d2 - b.d2;
			});
			return list.slice(0, count);
		}

		function drawLine(x1, y1, x2, y2, alpha, width) {
			ctx.beginPath();
			ctx.moveTo(x1, y1);
			ctx.lineTo(x2, y2);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', ' + alpha + ')';
			ctx.lineWidth = width;
			ctx.stroke();
		}

		function drawQuestion() {
			var fontSize = Math.max(48, Math.floor(scale * 0.22));
			var glow = 0.55 + Math.sin(pulseT * 1.1) * 0.12;

			ctx.save();
			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';
			ctx.font = '700 ' + fontSize + 'px "Space Grotesk", system-ui, sans-serif';

			/* Soft amber radial wash behind the glyph. */
			var wash = ctx.createRadialGradient(cx, cy, fontSize * 0.15, cx, cy, fontSize * 0.95);
			wash.addColorStop(0, 'rgba(' + AMBER_SOFT + ', ' + (0.18 * glow) + ')');
			wash.addColorStop(1, 'rgba(' + AMBER_SOFT + ', 0)');
			ctx.fillStyle = wash;
			ctx.beginPath();
			ctx.arc(cx, cy, fontSize * 0.95, 0, Math.PI * 2);
			ctx.fill();

			ctx.shadowColor = 'rgba(' + AMBER_SOFT + ', ' + (0.75 * glow) + ')';
			ctx.shadowBlur = fontSize * 0.35;

			var grad = ctx.createLinearGradient(cx, cy - fontSize * 0.45, cx, cy + fontSize * 0.45);
			grad.addColorStop(0, '#FFE8B3');
			grad.addColorStop(0.45, AMBER);
			grad.addColorStop(1, '#FFD27A');
			ctx.fillStyle = grad;
			ctx.fillText('?', cx, cy + fontSize * 0.04);

			ctx.shadowBlur = 0;
			ctx.restore();
		}

		function draw() {
			ctx.clearRect(0, 0, cssW, cssH);

			var positions = [];
			var i;
			for (i = 0; i < nodes.length; i++) {
				positions.push(nodePos(nodes[i]));
			}

			var pulse = 0.5 + 0.5 * Math.sin(pulseT * 0.9);

			/* Hub → node spokes. */
			for (i = 0; i < positions.length; i++) {
				var spokeAlpha = 0.12 + pulse * 0.1 + (i % 3) * 0.02;
				drawLine(cx, cy, positions[i].x, positions[i].y, spokeAlpha, 1);
			}

			/* Nearest-neighbor links (draw each pair once). */
			var drawn = {};
			for (i = 0; i < positions.length; i++) {
				var near = nearestNeighbors(positions, i, 2);
				var n;
				for (n = 0; n < near.length; n++) {
					var j = near[n].i;
					var key = i < j ? i + '-' + j : j + '-' + i;
					if (drawn[key]) {
						continue;
					}
					drawn[key] = true;
					var linkPulse = 0.08 + pulse * 0.14 + 0.04 * Math.sin(pulseT + nodes[i].phase);
					drawLine(
						positions[i].x,
						positions[i].y,
						positions[j].x,
						positions[j].y,
						linkPulse,
						1
					);
				}
			}

			/* Orbiting nodes. */
			for (i = 0; i < positions.length; i++) {
				var node = nodes[i];
				var p = positions[i];
				var r = node.size * (scale / 420);
				var nodeGlow = 0.35 + 0.2 * Math.sin(pulseT + node.phase);

				ctx.beginPath();
				ctx.arc(p.x, p.y, r * 2.4, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_PALE + ', ' + (0.08 + nodeGlow * 0.06) + ')';
				ctx.fill();

				ctx.beginPath();
				ctx.arc(p.x, p.y, r, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_SOFT + ', ' + (0.55 + nodeGlow * 0.25) + ')';
				ctx.shadowColor = 'rgba(' + AMBER_SOFT + ', 0.55)';
				ctx.shadowBlur = 8;
				ctx.fill();
				ctx.shadowBlur = 0;
			}

			drawQuestion();
		}

		function advance(dt) {
			var speedMul = ogcReducedMotion() ? 0.08 : 1;
			var i;
			for (i = 0; i < nodes.length; i++) {
				nodes[i].angle += nodes[i].speed * nodes[i].dir * dt * speedMul;
			}
			pulseT += dt * (ogcReducedMotion() ? 0.25 : 1);
		}

		function frame(ts) {
			if (!inView) {
				animId = 0;
				lastTs = 0;
				return;
			}

			animId = requestAnimationFrame(frame);
			var now = ts || performance.now();
			if (!lastTs) {
				lastTs = now;
			}
			var dt = Math.min(0.05, (now - lastTs) / 1000);
			lastTs = now;

			/* Reduced motion: drastically slow — keep a calm ambient pulse, never blank. */
			advance(dt);
			draw();
		}

		function sync() {
			stop();
			if (!inView) {
				draw();
				return;
			}
			lastTs = 0;
			animId = requestAnimationFrame(frame);
		}

		seedNodes();
		resize();
		draw();

		window.addEventListener('resize', function () {
			resize();
			draw();
		}, { passive: true });
		window.addEventListener('orientationchange', function () {
			setTimeout(function () {
				resize();
				draw();
			}, 120);
		}, { passive: true });

		var bindRm = typeof rmMq.addEventListener === 'function'
			? function (h) { rmMq.addEventListener('change', h); }
			: function (h) { rmMq.addListener(h); };
		bindRm(sync);

		if ('IntersectionObserver' in window) {
			new IntersectionObserver(function (entries) {
				inView = entries[0] && entries[0].isIntersecting;
				sync();
			}, { threshold: 0.05 }).observe(root);
		}

		sync();
	}

	function boot() {
		var roots = document.querySelectorAll('[data-ogc-support-network]');
		var i;
		for (i = 0; i < roots.length; i++) {
			initNetwork(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
