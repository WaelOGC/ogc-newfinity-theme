/**
 * OGC NewFinity — Contact hero "envelope send" (2D canvas).
 * Envelope with gently opening flap + soft upward particle drift.
 *
 * Drop-in: template-parts/hero-envelope-send.php (or any [data-ogc-envelope-send] root).
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

	/* Cycle timing (ms) — slow ambient loop. */
	var OPEN_MS = 2200;
	var SEND_MS = 2800;
	var HOLD_MS = 900;
	var RESET_MS = 1600;
	var CYCLE_MS = OPEN_MS + SEND_MS + HOLD_MS + RESET_MS;

	function easeInOut(t) {
		return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
	}

	function initEnvelope(root) {
		var canvas = root.querySelector('.ogc-envelope-send__canvas');
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
		var particles = [];
		var particleSeed = 0;

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
			cy = h * 0.52;
			scale = Math.min(w, h);
			canvas.width = Math.max(1, Math.floor(w * dpr));
			canvas.height = Math.max(1, Math.floor(h * dpr));
			canvas.style.width = w + 'px';
			canvas.style.height = h + 'px';
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
		}

		function cycleProgress() {
			var t = cycleT % CYCLE_MS;
			if (t < OPEN_MS) {
				return { phase: 'open', u: t / OPEN_MS };
			}
			t -= OPEN_MS;
			if (t < SEND_MS) {
				return { phase: 'send', u: t / SEND_MS };
			}
			t -= SEND_MS;
			if (t < HOLD_MS) {
				return { phase: 'hold', u: t / HOLD_MS };
			}
			t -= HOLD_MS;
			return { phase: 'reset', u: t / RESET_MS };
		}

		function flapOpenAmount(state) {
			if (state.phase === 'open') {
				return easeInOut(state.u);
			}
			if (state.phase === 'send' || state.phase === 'hold') {
				return 1;
			}
			/* reset — close gently */
			return 1 - easeInOut(state.u);
		}

		function spawnBurst() {
			var i;
			var count = 7;
			for (i = 0; i < count; i++) {
				var angle = -Math.PI / 2 + (Math.random() - 0.5) * 0.95;
				var speed = 18 + Math.random() * 28;
				particles.push({
					x: 0,
					y: 0,
					vx: Math.cos(angle) * speed,
					vy: Math.sin(angle) * speed - 8,
					life: 0,
					maxLife: 1.4 + Math.random() * 0.9,
					size: 1.6 + Math.random() * 2.2,
					drift: (Math.random() - 0.5) * 6
				});
			}
		}

		function updateParticles(dt, emitting) {
			if (emitting && particleSeed <= 0) {
				spawnBurst();
				particleSeed = 0.22 + Math.random() * 0.18;
			}
			if (emitting) {
				particleSeed -= dt;
			}

			var next = [];
			var i;
			for (i = 0; i < particles.length; i++) {
				var p = particles[i];
				p.life += dt;
				if (p.life >= p.maxLife) {
					continue;
				}
				p.x += (p.vx + p.drift) * dt;
				p.y += p.vy * dt;
				p.vy += 6 * dt;
				p.vx *= 0.992;
				next.push(p);
			}
			particles = next;
		}

		function drawEnvelope(openAmt) {
			var w = scale * 0.42;
			var h = scale * 0.28;
			var x = cx - w * 0.5;
			var y = cy - h * 0.35;
			var r = Math.max(4, scale * 0.012);

			/* Soft wash behind the envelope. */
			var wash = ctx.createRadialGradient(cx, cy, w * 0.15, cx, cy, w * 0.95);
			wash.addColorStop(0, 'rgba(' + AMBER_SOFT + ', 0.14)');
			wash.addColorStop(1, 'rgba(' + AMBER_SOFT + ', 0)');
			ctx.fillStyle = wash;
			ctx.beginPath();
			ctx.arc(cx, cy, w * 0.95, 0, Math.PI * 2);
			ctx.fill();

			var bodyGrad = ctx.createLinearGradient(x, y, x, y + h);
			bodyGrad.addColorStop(0, 'rgba(' + AMBER_PALE + ', 0.22)');
			bodyGrad.addColorStop(0.5, 'rgba(' + AMBER_SOFT + ', 0.16)');
			bodyGrad.addColorStop(1, 'rgba(255, 176, 0, 0.12)');

			ctx.beginPath();
			ctx.moveTo(x + r, y);
			ctx.lineTo(x + w - r, y);
			ctx.quadraticCurveTo(x + w, y, x + w, y + r);
			ctx.lineTo(x + w, y + h - r);
			ctx.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
			ctx.lineTo(x + r, y + h);
			ctx.quadraticCurveTo(x, y + h, x, y + h - r);
			ctx.lineTo(x, y + r);
			ctx.quadraticCurveTo(x, y, x + r, y);
			ctx.closePath();
			ctx.fillStyle = bodyGrad;
			ctx.fill();
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.55)';
			ctx.lineWidth = Math.max(1.25, scale * 0.004);
			ctx.stroke();

			/* Inner V fold lines (closed pocket suggestion). */
			ctx.beginPath();
			ctx.moveTo(x + w * 0.08, y + h * 0.12);
			ctx.lineTo(cx, y + h * 0.52);
			ctx.lineTo(x + w * 0.92, y + h * 0.12);
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.22)';
			ctx.lineWidth = Math.max(1, scale * 0.003);
			ctx.stroke();

			/* Open flap — hinged at top edge, rotates upward. */
			var flapH = h * 0.48;
			var lift = openAmt * flapH * 0.92;
			var skew = openAmt * w * 0.02;

			ctx.save();
			ctx.beginPath();
			ctx.moveTo(x + r * 0.5, y);
			ctx.lineTo(x + w - r * 0.5, y);
			ctx.lineTo(cx + skew, y - lift);
			ctx.closePath();

			var flapGrad = ctx.createLinearGradient(cx, y - lift, cx, y);
			flapGrad.addColorStop(0, 'rgba(' + AMBER_PALE + ', ' + (0.35 + openAmt * 0.25) + ')');
			flapGrad.addColorStop(1, 'rgba(' + AMBER_SOFT + ', ' + (0.18 + openAmt * 0.12) + ')');
			ctx.fillStyle = flapGrad;
			ctx.fill();
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', ' + (0.45 + openAmt * 0.25) + ')';
			ctx.lineWidth = Math.max(1.25, scale * 0.004);
			ctx.stroke();
			ctx.restore();

			/* Subtle amber rim glow on body. */
			ctx.shadowColor = 'rgba(' + AMBER_SOFT + ', 0.35)';
			ctx.shadowBlur = scale * 0.04;
			ctx.strokeStyle = 'rgba(' + AMBER_SOFT + ', 0.2)';
			ctx.strokeRect(x, y, w, h);
			ctx.shadowBlur = 0;

			return { mouthX: cx, mouthY: y - lift * 0.35, bodyY: y };
		}

		function drawParticles(origin) {
			var i;
			for (i = 0; i < particles.length; i++) {
				var p = particles[i];
				var t = p.life / p.maxLife;
				var alpha = (1 - t) * (1 - t) * 0.85;
				var px = origin.mouthX + p.x * (scale / 320);
				var py = origin.mouthY + p.y * (scale / 320);
				var r = p.size * (scale / 420) * (1 - t * 0.35);

				ctx.beginPath();
				ctx.arc(px, py, r * 2.2, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_PALE + ', ' + (alpha * 0.2) + ')';
				ctx.fill();

				ctx.beginPath();
				ctx.arc(px, py, r, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_SOFT + ', ' + alpha + ')';
				ctx.shadowColor = 'rgba(' + AMBER_SOFT + ', 0.65)';
				ctx.shadowBlur = 10;
				ctx.fill();
				ctx.shadowBlur = 0;
			}
		}

		function draw() {
			ctx.clearRect(0, 0, cssW, cssH);

			var state = cycleProgress();
			var openAmt = flapOpenAmount(state);

			if (ogcReducedMotion()) {
				/* Near-static: flap gently ajar, no particle churn. */
				openAmt = 0.55;
			}

			var origin = drawEnvelope(openAmt);

			if (!ogcReducedMotion()) {
				drawParticles(origin);
			} else {
				/* One soft static sparkle above the opening. */
				ctx.beginPath();
				ctx.arc(origin.mouthX, origin.mouthY - scale * 0.04, scale * 0.008, 0, Math.PI * 2);
				ctx.fillStyle = 'rgba(' + AMBER_SOFT + ', 0.45)';
				ctx.shadowColor = 'rgba(' + AMBER_SOFT + ', 0.55)';
				ctx.shadowBlur = 8;
				ctx.fill();
				ctx.shadowBlur = 0;
			}
		}

		function advance(dt) {
			var speedMul = ogcReducedMotion() ? 0.06 : 1;
			var prevPhase = cycleProgress().phase;
			cycleT += dt * 1000 * speedMul;
			var state = cycleProgress();

			if (!ogcReducedMotion()) {
				var emitting = state.phase === 'send';
				if (prevPhase !== 'send' && state.phase === 'send') {
					particleSeed = 0;
					spawnBurst();
				}
				if (state.phase === 'reset' && state.u > 0.85) {
					particles = [];
				}
				updateParticles(dt, emitting);
			} else {
				particles = [];
			}
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
		var roots = document.querySelectorAll('[data-ogc-envelope-send]');
		var i;
		for (i = 0; i < roots.length; i++) {
			initEnvelope(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
