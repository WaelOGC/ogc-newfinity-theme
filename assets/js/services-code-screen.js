/**
 * OGC NewFinity — Our Services hero "code screen" panel.
 * Fixed-position character grid with subtle live flicker (not matrix rain).
 *
 * Drop-in: template-parts/hero-code-screen.php (or any [data-ogc-code-screen] root).
 */
(function () {
	'use strict';

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	function ogcReducedMotion() {
		return rmMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}

	var CELL_TARGET = 21;
	var TICK_MS = 110;
	var TICK_MS_REDUCED = 900;
	var UPDATE_RATE = 0.02;
	var TOKEN_RATE = 0.06;
	var BRAND_EVERY = 90;
	var BRAND_HOLD = 40;
	var BRAND_FADE = 28;
	var BRAND = 'OGCNEWFINITY';

	var CHARS =
		'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ{}<>/;=+*#$' +
		'アイウエオカキクケコサシスセソタチツテトナニヌネノハヒフヘホマミムメモヤユヨラリルレロワヲン';

	var TOKENS = ['JS', 'PY', 'TS', '{}', '</>', 'AI', 'GO', 'SQL', 'API', 'CSS'];

	function randChar() {
		return CHARS.charAt((Math.random() * CHARS.length) | 0);
	}

	function randToken() {
		return TOKENS[(Math.random() * TOKENS.length) | 0];
	}

	function randOpacity() {
		return 0.2 + Math.random() * 0.5;
	}

	function initScreen(root) {
		var canvas = root.querySelector('.ogc-code-screen__canvas');
		var bubble = root.querySelector('.ogc-code-screen__bubble');
		var bubbleChar = root.querySelector('.ogc-code-screen__bubble-char');
		var inner = root.querySelector('.ogc-code-screen__inner');
		if (!canvas || !inner) { return; }

		var ctx = canvas.getContext('2d');
		if (!ctx) { return; }

		var cols = 0;
		var rows = 0;
		var cellW = CELL_TARGET;
		var cellH = CELL_TARGET;
		var cells = [];
		var tickCount = 0;
		var brandLeft = 0;
		var brandRow = 0;
		var brandStartCol = 0;
		var inView = true;
		var tickTimer = 0;
		var hoverCol = -1;
		var hoverRow = -1;

		function stopTick() {
			if (tickTimer) {
				clearInterval(tickTimer);
				tickTimer = 0;
			}
		}

		function buildGrid() {
			var w = inner.clientWidth || 320;
			var h = inner.clientHeight || 240;
			var dpr = Math.min(window.devicePixelRatio || 1, 2);
			canvas.width = Math.max(1, Math.floor(w * dpr));
			canvas.height = Math.max(1, Math.floor(h * dpr));
			canvas.style.width = w + 'px';
			canvas.style.height = h + 'px';
			ctx.setTransform(dpr, 0, 0, dpr, 0, 0);

			cols = Math.max(4, Math.floor(w / CELL_TARGET));
			rows = Math.max(4, Math.floor(h / CELL_TARGET));
			cellW = w / cols;
			cellH = h / rows;

			var next = new Array(cols * rows);
			var i;
			for (i = 0; i < next.length; i++) {
				var prev = cells[i];
				if (prev && !prev.brand) {
					next[i] = prev;
				} else {
					next[i] = {
						ch: randChar(),
						op: randOpacity(),
						token: false,
						brand: false
					};
				}
			}
			cells = next;
			brandLeft = 0;
		}

		function activateBrand() {
			brandRow = Math.max(0, Math.min(rows - 1, Math.floor(rows * 0.48)));
			brandStartCol = Math.max(0, Math.floor((cols - BRAND.length) / 2));
			if (brandStartCol + BRAND.length > cols) {
				brandStartCol = Math.max(0, cols - BRAND.length);
			}
			var i;
			for (i = 0; i < BRAND.length; i++) {
				var c = brandStartCol + i;
				if (c >= cols) { break; }
				var cell = cells[brandRow * cols + c];
				if (!cell) { continue; }
				cell.ch = BRAND.charAt(i);
				cell.op = 1;
				cell.token = false;
				cell.brand = true;
			}
			brandLeft = BRAND_HOLD + BRAND_FADE;
		}

		function clearBrand() {
			var i;
			for (i = 0; i < BRAND.length; i++) {
				var c = brandStartCol + i;
				if (c < 0 || c >= cols) { continue; }
				var cell = cells[brandRow * cols + c];
				if (!cell || !cell.brand) { continue; }
				cell.ch = randChar();
				cell.op = randOpacity();
				cell.token = false;
				cell.brand = false;
			}
			brandLeft = 0;
		}

		function tick() {
			if (!inView || !cells.length) { return; }

			tickCount += 1;

			if (brandLeft > 0) {
				brandLeft -= 1;
				if (brandLeft === 0) {
					clearBrand();
				} else if (brandLeft <= BRAND_FADE) {
					var fade = brandLeft / BRAND_FADE;
					var i;
					for (i = 0; i < BRAND.length; i++) {
						var c = brandStartCol + i;
						if (c < 0 || c >= cols) { continue; }
						var cell = cells[brandRow * cols + c];
						if (cell && cell.brand) {
							cell.op = 0.35 + fade * 0.65;
						}
					}
				}
			} else if (tickCount % BRAND_EVERY === 0) {
				activateBrand();
			}

			var updates = Math.max(1, Math.floor(cells.length * UPDATE_RATE));
			var u;
			for (u = 0; u < updates; u++) {
				var idx = (Math.random() * cells.length) | 0;
				var cell = cells[idx];
				if (!cell || cell.brand) { continue; }
				if (Math.random() < TOKEN_RATE) {
					cell.ch = randToken();
					cell.token = true;
					cell.op = 0.75 + Math.random() * 0.2;
				} else {
					cell.ch = randChar();
					cell.token = false;
					cell.op = randOpacity();
				}
			}

			draw();
		}

		function draw() {
			var w = inner.clientWidth || 320;
			var h = inner.clientHeight || 240;
			ctx.clearRect(0, 0, w, h);

			var minCell = Math.min(cellW, cellH);
			var fontSize = Math.max(11, Math.floor(minCell * 0.72));
			var tokenSize = Math.max(9, Math.floor(minCell * 0.55));
			var brandSize = Math.max(12, Math.floor(minCell * 0.8));
			var cx;
			var cy;
			var cell;
			var x;
			var y;

			ctx.textAlign = 'center';
			ctx.textBaseline = 'middle';

			for (cy = 0; cy < rows; cy++) {
				for (cx = 0; cx < cols; cx++) {
					cell = cells[cy * cols + cx];
					if (!cell) { continue; }
					x = (cx + 0.5) * cellW;
					y = (cy + 0.5) * cellH;

					if (cell.brand) {
						ctx.font = '700 ' + brandSize + 'px "Space Grotesk", ui-monospace, monospace';
						ctx.shadowColor = 'rgba(255, 210, 122, 0.85)';
						ctx.shadowBlur = 12;
						ctx.fillStyle = 'rgba(255, 248, 230, ' + cell.op + ')';
						ctx.fillText(cell.ch, x, y);
						ctx.shadowBlur = 0;
					} else if (cell.token) {
						ctx.font = '700 ' + tokenSize + 'px ui-monospace, "Cascadia Code", Consolas, monospace';
						ctx.shadowColor = 'rgba(255, 210, 122, 0.55)';
						ctx.shadowBlur = 8;
						ctx.fillStyle = 'rgba(255, 210, 122, ' + cell.op + ')';
						ctx.fillText(cell.ch, x, y);
						ctx.shadowBlur = 0;
					} else {
						ctx.font = '500 ' + fontSize + 'px ui-monospace, "Cascadia Code", Consolas, monospace';
						ctx.fillStyle = 'rgba(255, 210, 122, ' + cell.op + ')';
						ctx.fillText(cell.ch, x, y);
					}
				}
			}
		}

		function cellFromEvent(e) {
			var rect = canvas.getBoundingClientRect();
			var clientX = e.clientX;
			var clientY = e.clientY;
			if (e.touches && e.touches.length) {
				clientX = e.touches[0].clientX;
				clientY = e.touches[0].clientY;
			} else if (e.changedTouches && e.changedTouches.length) {
				clientX = e.changedTouches[0].clientX;
				clientY = e.changedTouches[0].clientY;
			}
			var x = clientX - rect.left;
			var y = clientY - rect.top;
			var col = Math.floor(x / cellW);
			var row = Math.floor(y / cellH);
			if (col < 0 || row < 0 || col >= cols || row >= rows) {
				return null;
			}
			return { col: col, row: row };
		}

		function showBubble(col, row) {
			if (!bubble || !bubbleChar) { return; }
			var cell = cells[row * cols + col];
			if (!cell) {
				hideBubble();
				return;
			}
			hoverCol = col;
			hoverRow = row;
			bubbleChar.textContent = cell.ch;
			bubble.hidden = false;

			var bx = (col + 0.5) * cellW;
			var by = row * cellH;
			var iw = inner.clientWidth || 320;
			var half = bubble.offsetWidth / 2 || 18;
			var left = Math.max(half + 4, Math.min(iw - half - 4, bx));
			bubble.style.left = left + 'px';
			bubble.style.top = Math.max(8, by - 6) + 'px';
		}

		function hideBubble() {
			hoverCol = -1;
			hoverRow = -1;
			if (bubble) {
				bubble.hidden = true;
			}
		}

		function onMove(e) {
			var pos = cellFromEvent(e);
			if (!pos) {
				hideBubble();
				return;
			}
			showBubble(pos.col, pos.row);
		}

		function onLeave() {
			hideBubble();
		}

		function onTouchStart(e) {
			var pos = cellFromEvent(e);
			if (pos) {
				showBubble(pos.col, pos.row);
			}
		}

		function onTouchEnd() {
			hideBubble();
		}

		function sync() {
			stopTick();
			if (!inView) {
				draw();
				return;
			}
			draw();
			var ms = ogcReducedMotion() ? TICK_MS_REDUCED : TICK_MS;
			tickTimer = setInterval(tick, ms);
		}

		function resize() {
			buildGrid();
			draw();
			if (hoverCol >= 0 && hoverRow >= 0) {
				showBubble(
					Math.min(hoverCol, cols - 1),
					Math.min(hoverRow, rows - 1)
				);
			}
		}

		buildGrid();

		/* Seed a denser initial grid so the first paint isn't sparse. */
		var s;
		for (s = 0; s < cells.length; s++) {
			cells[s].ch = randChar();
			cells[s].op = randOpacity();
		}

		canvas.addEventListener('mousemove', onMove, { passive: true });
		canvas.addEventListener('mouseleave', onLeave, { passive: true });
		canvas.addEventListener('touchstart', onTouchStart, { passive: true });
		canvas.addEventListener('touchend', onTouchEnd, { passive: true });
		canvas.addEventListener('touchcancel', onTouchEnd, { passive: true });

		window.addEventListener('resize', resize, { passive: true });
		window.addEventListener('orientationchange', function () {
			setTimeout(resize, 120);
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
		var roots = document.querySelectorAll('[data-ogc-code-screen]');
		var i;
		for (i = 0; i < roots.length; i++) {
			initScreen(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
