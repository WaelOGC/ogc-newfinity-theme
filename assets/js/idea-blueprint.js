/**
 * OGC NewFinity — Submit Your Idea hero "idea blueprint" (2D canvas).
 * Lightbulb outline gently morphs into a blueprint grid + construction marks.
 *
 * Drop-in: template-parts/hero-idea-blueprint.php (or any [data-ogc-idea-blueprint] root).
 */
(function () {
	'use strict';

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');

	/* Brand accents already used across other hero canvases. */
	var AMBER = '#FFB000';
	var AMBER_SOFT = '255, 210, 122';
	var AMBER_PALE = '255, 232, 179';

	/* Cycle timing (ms) — slow ambient loop. */
	var BULB_HOLD_MS = 2200;
	var TO_BLUEPRINT_MS = 2400;
	var BLUEPRINT_HOLD_MS = 2600;
	var TO_BULB_MS = 2200;
	var CYCLE_MS = BULB_HOLD_MS + TO_BLUEPRINT_MS + BLUEPRINT_HOLD_MS + TO_BULB_MS;

	function easeInOut(t) {
		return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
	}

	function initBlueprint(root) {
		var canvas = root.querySelector('.ogc-idea-blueprint__canvas');
		if (!canvas) {
			return;
		}

		var ctx = canvas.getContext('2d');
		if (!ctx) {
			return;
		}

		var cssW = 0;
		var cssH = 0;
		var cx = 0;
		var cy = 0;
		var scale = 1;
		var inView = true;
		var animId = 0;
		var lastTs = 0;
		var cycleT = 0;

		function stop() {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = 0;
			}
			lastTs = 0;
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

		function cycleProgress() {
			var t = cycleT % CYCLE_MS;
			if (t < BULB_HOLD_MS) {
				return { phase: 'bulb', u: t / BULB_HOLD_MS, bulb: 1, plan: 0 };
			}
			t -= BULB_HOLD_MS;
			if (t < TO_BLUEPRINT_MS) {
				var u = easeInOut(t / TO_BLUEPRINT_MS);
				return { phase: 'to-plan', u: u, bulb: 1 - u, plan: u };
			}
			t -= TO_BLUEPRINT_MS;
			if (t < BLUEPRINT_HOLD_MS) {
				return { phase: 'plan', u: t / BLUEPRINT_HOLD_MS, bulb: 0, plan: 1 };
			}
			t -= BLUEPRINT_HOLD_MS;
			var v = easeInOut(t / TO_BULB_MS);
			return { phase: 'to-bulb', u: v, bulb: v, plan: 1 - v };
		}

		function drawWash() {
			var r = scale * 0.42;
			var wash = ctx.createRadialGradient(cx, cy, r * 0.12, cx, cy, r);
			wash.addColorStop(0, 'rgba(' + AMBER_SOFT + ', 0.14)');
			wash.addColorStop(1, 'rgba(' + AMBER_SOFT + ', 0)');
			ctx.fillStyle = wash;
			ctx.beginPath();
			ctx.arc(cx, cy, r, 0, Math.PI * 2);
			ctx.fill();
		}

		function drawLightbulb(alpha) {
			if (alpha <= 0.01) {
				return;
			}

			var s = scale * 0.22;
			var bulbCy = cy - s * 0.18;
			var bulbR = s * 0.72;
			var lw = Math.max(1.4, scale * 0.0045);

			ctx.save();
			ctx.globalAlpha = alpha;

			/* Soft glow inside the glass. */
			var glow = ctx.createRadialGradient(cx, bulbCy, bulbR * 0.1, cx, bulbCy, bulbR * 1.15);
			glow.addColorStop(0, 'rgba(' + AMBER_PALE + ', 0.28)');
			glow.addColorStop(0.55, 'rgba(' + AMBER_SOFT + ', 0.12)');
			glow.addColorStop(1, 'rgba(' + AMBER_SOFT + ', 0)');
			ctx.fillStyle = glow;
			ctx.beginPath();
			ctx.arc(cx, bulbCy, bulbR * 1.15, 0, Math.PI * 2);
			ctx.fill();

			/* Bulb outline (circle + slight neck flare). */
			ctx.beginPath();
			ctx.arc(cx, bulbCy, bulbR, Math.PI * 0.18, Math.PI * 0.82, true);
			ctx.quadraticCurveTo(cx + bulbR * 0.42, bulbCy + bulbR * 0.95, cx + s * 0.28, bulbCy + bulbR * 1.15);
			ctx.lineTo(cx - s * 0.28, bulbCy + bulbR * 1.15);
			ctx.quadraticCurveTo(cx - bulbR * 0.42, bulbCy + bulbR * 0.95, cx - bulbR * Math.cos(Math.PI * 0.18), bulbCy + bulbR * Math.sin(Math.PI * 0.18));
			ctx.closePath();

			var glass = ctx.createLinearGradient(cx, bulbCy - bulbR, cx, bulbCy + bulbR);
			glass.addColorStop(0, 'rgba(' + AMBER_PALE + ', 0.22)');
			glass.addColorStop(1, 'rgba(255, 176, 0, 0.1)');
			ctx.fillStyle = glass;
			ctx.fill();
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.7)';
			ctx.lineWidth = lw;
			ctx.lineJoin = 'round';
			ctx.stroke();

			/* Filament. */
			ctx.beginPath();
			ctx.moveTo(cx - s * 0.18, bulbCy + s * 0.05);
			ctx.lineTo(cx - s * 0.06, bulbCy - s * 0.22);
			ctx.lineTo(cx + s * 0.06, bulbCy + s * 0.08);
			ctx.lineTo(cx + s * 0.18, bulbCy - s * 0.18);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.55)';
			ctx.lineWidth = Math.max(1.1, scale * 0.0035);
			ctx.stroke();

			/* Base / screw collar. */
			var baseTop = bulbCy + bulbR * 1.12;
			var baseW = s * 0.58;
			var baseH = s * 0.38;
			var i;
			for (i = 0; i < 3; i++) {
				var y = baseTop + i * (baseH / 3);
				ctx.beginPath();
				ctx.moveTo(cx - baseW * 0.5 + i * 2, y);
				ctx.lineTo(cx + baseW * 0.5 - i * 2, y);
				ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', ' + (0.55 - i * 0.08) + ')';
				ctx.lineWidth = lw * 0.9;
				ctx.stroke();
			}

			ctx.beginPath();
			ctx.moveTo(cx - baseW * 0.28, baseTop + baseH * 0.95);
			ctx.lineTo(cx + baseW * 0.28, baseTop + baseH * 0.95);
			ctx.quadraticCurveTo(cx, baseTop + baseH * 1.35, cx - baseW * 0.28, baseTop + baseH * 0.95);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.5)';
			ctx.stroke();

			ctx.restore();
		}

		function drawCornerBracket(x, y, dx, dy, len, alpha) {
			ctx.beginPath();
			ctx.moveTo(x + dx * len, y);
			ctx.lineTo(x, y);
			ctx.lineTo(x, y + dy * len);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', ' + (0.55 * alpha) + ')';
			ctx.lineWidth = Math.max(1.1, scale * 0.0032);
			ctx.stroke();
		}

		function drawBlueprint(alpha) {
			if (alpha <= 0.01) {
				return;
			}

			var box = scale * 0.38;
			var left = cx - box * 0.5;
			var top = cy - box * 0.5;
			var right = left + box;
			var bottom = top + box;
			var lw = Math.max(1, scale * 0.003);
			var spread = 0.55 + alpha * 0.45;

			ctx.save();
			ctx.globalAlpha = alpha;

			/* Outer frame. */
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.45)';
			ctx.lineWidth = lw;
			ctx.strokeRect(left, top, box, box);

			/* Grid. */
			var cells = 5;
			var step = box / cells;
			var g;
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.16)';
			ctx.lineWidth = Math.max(0.75, scale * 0.002);
			for (g = 1; g < cells; g++) {
				var gx = left + g * step;
				var gy = top + g * step;
				ctx.beginPath();
				ctx.moveTo(gx, top);
				ctx.lineTo(gx, bottom);
				ctx.stroke();
				ctx.beginPath();
				ctx.moveTo(left, gy);
				ctx.lineTo(right, gy);
				ctx.stroke();
			}

			/* Construction rays from center. */
			var rays = [
				{ x: 0.72, y: -0.55 },
				{ x: -0.68, y: -0.48 },
				{ x: 0.58, y: 0.7 },
				{ x: -0.62, y: 0.58 },
				{ x: 0.15, y: -0.82 }
			];
			var r;
			for (r = 0; r < rays.length; r++) {
				var rx = cx + rays[r].x * box * 0.55 * spread;
				var ry = cy + rays[r].y * box * 0.55 * spread;
				ctx.beginPath();
				ctx.moveTo(cx, cy);
				ctx.lineTo(rx, ry);
				ctx.strokeStyle = 'rgba(' + AMBER_PALE + ', ' + (0.22 + r * 0.03) + ')';
				ctx.lineWidth = Math.max(0.8, scale * 0.0022);
				ctx.stroke();

				/* Tick mark at ray end. */
				ctx.beginPath();
				ctx.arc(rx, ry, scale * 0.006, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_SOFT + ', 0.55)';
				ctx.fill();
			}

			/* Corner brackets radiating slightly outward. */
			var inset = box * 0.06;
			var blen = box * 0.11 * spread;
			drawCornerBracket(left + inset, top + inset, 1, 1, blen, alpha);
			drawCornerBracket(right - inset, top + inset, -1, 1, blen, alpha);
			drawCornerBracket(left + inset, bottom - inset, 1, -1, blen, alpha);
			drawCornerBracket(right - inset, bottom - inset, -1, -1, blen, alpha);

			/* Soft center crosshair. */
			var ch = box * 0.08;
			ctx.beginPath();
			ctx.moveTo(cx - ch, cy);
			ctx.lineTo(cx + ch, cy);
			ctx.moveTo(cx, cy - ch);
			ctx.lineTo(cx, cy + ch);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.4)';
			ctx.lineWidth = lw;
			ctx.stroke();

			ctx.restore();
		}

		function draw(state) {
			ctx.clearRect(0, 0, cssW, cssH);
			drawWash();

			var bulbA = state.bulb;
			var planA = state.plan;

			if (rmMq.matches) {
				/* Near-static blend — both faintly present, no morph churn. */
				bulbA = 0.72;
				planA = 0.28;
			}

			drawLightbulb(bulbA);
			drawBlueprint(planA);
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

			var speedMul = rmMq.matches ? 0.06 : 1;
			cycleT += dt * 1000 * speedMul;
			draw(cycleProgress());
		}

		function sync() {
			stop();
			if (!inView) {
				draw(cycleProgress());
				return;
			}
			lastTs = 0;
			animId = requestAnimationFrame(frame);
		}

		resize();
		draw(cycleProgress());

		window.addEventListener('resize', function () {
			resize();
			draw(cycleProgress());
		}, { passive: true });
		window.addEventListener('orientationchange', function () {
			setTimeout(function () {
				resize();
				draw(cycleProgress());
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
		var roots = document.querySelectorAll('[data-ogc-idea-blueprint]');
		var i;
		for (i = 0; i < roots.length; i++) {
			initBlueprint(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
