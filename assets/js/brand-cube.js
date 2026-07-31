/**
 * OGC NewFinity — hero brand cube.
 * Baked CanvasTextures (title + description + CTA button) on a rounded MeshPhysicalMaterial box.
 * CTA is UV-hit tested — no separate HTML button. Drag + inertia. No AI logic.
 */
(function () {
	'use strict';

	var root   = document.getElementById('ogc-brand-cube');
	var canvas = document.getElementById('ogc-brand-cube-canvas');
	var hintEl = root ? root.querySelector('[data-cube-hint]') : null;
	if (!root || !canvas || typeof THREE === 'undefined') { return; }

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	function ogcReducedMotion() {
		return rmMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}
	var HALF = 1.05;
	var TEX  = 1024;

	/* Button region on the 1024² canvas (pixels, origin top-left). */
	var BTN = { x: 272, y: 780, w: 480, h: 96 };

	/* Convert canvas-pixel rect → UV (flipY=true → v grows upward). */
	var BTN_UV = {
		u0: BTN.x / TEX,
		u1: (BTN.x + BTN.w) / TEX,
		v0: 1 - (BTN.y + BTN.h) / TEX,
		v1: 1 - BTN.y / TEX
	};

	var FACES = [
		{ id:'about',      title:'About us',       btn:'Learn more',       url:'/about-ogc-newfinity/',
		  body:'OGC NewFinity is a digital agency based in The Hague, Netherlands, founded in 2022. We build technology with a simple standard: it has to actually work for the people using it.' },
		{ id:'services',   title:'Our services',   btn:'View services',    url:'/services/',
		  body:'Software, WordPress, UI/UX, marketing strategy, cybersecurity, and AI — six capabilities, one team.' },
		{ id:'mission',    title:'Our mission',    btn:'Read our mission', url:'/our-mission/',
		  body:'Ethical innovation, built with intention. We measure success by whether our work actually earns your trust.' },
		{ id:'contact',    title:'Contact us',     btn:'Get in touch',     url:'/contact/',
		  body:'Based in The Hague, Netherlands. contact@ogcnewfinity.com — every message gets a real reply within 48 hours.' },
		{ id:'feedback',   title:'Leave feedback', btn:'Share feedback',   url:'/support/',
		  body:"Had an experience with us? We'd like to hear about it, good or bad." },
		{ id:'ai-support', title:'AI support',     btn:'Chat now',         url:'/support/#ai-support',
		  body:'Talk to our assistant for quick answers about our services and process.' }
	];

	/* Override from wp_localize_script when available */
	if (window.ogcBrandCube && window.ogcBrandCube.faces) {
		for (var fi = 0; fi < 6; fi++) {
			if (window.ogcBrandCube.faces[fi]) {
				FACES[fi].title = window.ogcBrandCube.faces[fi].title || FACES[fi].title;
				FACES[fi].body  = window.ogcBrandCube.faces[fi].body  || FACES[fi].body;
				FACES[fi].btn   = window.ogcBrandCube.faces[fi].btn   || FACES[fi].btn;
				FACES[fi].url   = window.ogcBrandCube.faces[fi].url   || FACES[fi].url;
			}
		}
	}

	/* BoxGeometry material slots (+X,-X,+Y,-Y,+Z,-Z) → FACES index */
	var SLOT_TO_FACE = [3, 4, 2, 5, 0, 1];

	var renderer, scene, camera, cube;
	var titleTex = [], revealTex = [], faceMats = [];
	var animId = 0, inView = true;
	var isDrag = false, didDrag = false, lx = 0, ly = 0, vx = 0, vy = 0;
	var expanded = -1;
	var raycaster = new THREE.Raycaster();
	var ptr = new THREE.Vector2();

	/* ── Drawing helpers ────────────────────────────────────────────────── */
	function roundRect(ctx, x, y, w, h, r) {
		ctx.beginPath();
		ctx.moveTo(x + r, y);
		ctx.arcTo(x + w, y,     x + w, y + h, r);
		ctx.arcTo(x + w, y + h, x,     y + h, r);
		ctx.arcTo(x,     y + h, x,     y,     r);
		ctx.arcTo(x,     y,     x + w, y,     r);
		ctx.closePath();
	}

	function drawLines(ctx, text, cx, startY, maxW, lh) {
		var words = text.split(' '), line = '', out = [];
		for (var i = 0; i < words.length; i++) {
			var test = line + (line ? ' ' : '') + words[i];
			if (ctx.measureText(test).width > maxW && line) {
				out.push(line); line = words[i];
			} else { line = test; }
		}
		if (line) out.push(line);
		var y = startY - ((out.length - 1) * lh) / 2;
		for (var j = 0; j < out.length; j++) ctx.fillText(out[j], cx, y + j * lh);
	}

	function makeTex(face, revealed) {
		var c = document.createElement('canvas');
		c.width = c.height = TEX;
		var ctx = c.getContext('2d');

		ctx.fillStyle = '#111113';
		ctx.fillRect(0, 0, TEX, TEX);

		var g = ctx.createRadialGradient(TEX * 0.5, TEX * 0.38, TEX * 0.06, TEX * 0.5, TEX * 0.5, TEX * 0.72);
		g.addColorStop(0, 'rgba(255,176,0,0.08)');
		g.addColorStop(1, 'rgba(0,0,0,0.55)');
		ctx.fillStyle = g;
		ctx.fillRect(0, 0, TEX, TEX);

		var pad = 68, r = 32;
		roundRect(ctx, pad, pad, TEX - pad * 2, TEX - pad * 2, r);
		ctx.fillStyle = 'rgba(8,8,8,0.72)';
		ctx.fill();
		ctx.strokeStyle = 'rgba(255,176,0,0.32)';
		ctx.lineWidth = 4;
		ctx.stroke();

		ctx.textAlign = 'center';
		ctx.textBaseline = 'middle';

		if (!revealed) {
			ctx.fillStyle = '#FFB000';
			ctx.font = 'bold 92px "Space Grotesk",Inter,sans-serif';
			drawLines(ctx, face.title, TEX / 2, TEX / 2, TEX - pad * 2 - 60, 108);
		} else {
			ctx.fillStyle = '#FFB000';
			ctx.font = 'bold 54px "Space Grotesk",Inter,sans-serif';
			drawLines(ctx, face.title, TEX / 2, pad + 88, TEX - pad * 2 - 40, 64);

			ctx.fillStyle = '#C9C9C9';
			ctx.font = '400 32px Inter,sans-serif';
			drawLines(ctx, face.body, TEX / 2, TEX / 2 - 10, TEX - pad * 2 - 80, 46);

			/* Baked .ogc-btn look — outlined amber pill */
			roundRect(ctx, BTN.x, BTN.y, BTN.w, BTN.h, 14);
			ctx.fillStyle = 'rgba(10,10,10,0.55)';
			ctx.fill();
			ctx.strokeStyle = '#FFB000';
			ctx.lineWidth = 5;
			ctx.stroke();
			ctx.fillStyle = '#FFB000';
			ctx.font = '600 36px Inter,sans-serif';
			ctx.fillText(face.btn, TEX / 2, BTN.y + BTN.h / 2);
		}

		var t = new THREE.CanvasTexture(c);
		t.anisotropy = 4;
		return t;
	}

	function roundedBox(w, h, d, radius, segs) {
		segs = segs || 4;
		radius = Math.min(radius, w / 2, h / 2, d / 2);
		var g = new THREE.BoxGeometry(w, h, d, segs * 2, segs * 2, segs * 2);
		var p = g.attributes.position, v = new THREE.Vector3();
		var bx = w / 2 - radius, by = h / 2 - radius, bz = d / 2 - radius;
		for (var i = 0; i < p.count; i++) {
			v.fromBufferAttribute(p, i);
			var ox = v.x, oy = v.y, oz = v.z;
			v.x = Math.max(-bx, Math.min(bx, v.x));
			v.y = Math.max(-by, Math.min(by, v.y));
			v.z = Math.max(-bz, Math.min(bz, v.z));
			var dx = ox - v.x, dy = oy - v.y, dz = oz - v.z;
			var len = Math.sqrt(dx * dx + dy * dy + dz * dz);
			if (len > 0) { v.x += dx * radius / len; v.y += dy * radius / len; v.z += dz * radius / len; }
			p.setXYZ(i, v.x, v.y, v.z);
		}
		g.computeVertexNormals();
		return g;
	}

	function absUrl(path) {
		if (/^https?:\/\//i.test(path)) return path;
		var base = (window.ogcBrandCube && window.ogcBrandCube.homeUrl) || window.location.origin;
		return base.replace(/\/$/, '') + (path[0] === '/' ? path : '/' + path);
	}

	function hitButton(uv) {
		if (!uv) return false;
		return uv.x >= BTN_UV.u0 && uv.x <= BTN_UV.u1 &&
		       uv.y >= BTN_UV.v0 && uv.y <= BTN_UV.v1;
	}

	function applyTex(faceIdx, revealed) {
		for (var s = 0; s < 6; s++) {
			if (SLOT_TO_FACE[s] === faceIdx) {
				faceMats[s].map = revealed ? revealTex[faceIdx] : titleTex[faceIdx];
				faceMats[s].needsUpdate = true;
				faceMats[s].map.needsUpdate = true;
			}
		}
	}

	function setExpanded(idx) {
		if (expanded >= 0 && expanded !== idx) applyTex(expanded, false);
		if (expanded === idx) {
			applyTex(idx, false);
			expanded = -1;
		} else {
			applyTex(idx, true);
			expanded = idx;
		}
		if (hintEl) hintEl.classList.add('is-hidden');
	}

	function evtXY(e) {
		var t = (e.touches && e.touches[0]) || (e.changedTouches && e.changedTouches[0]) || e;
		return { x: t.clientX, y: t.clientY };
	}

	function setPtr(e) {
		var r = canvas.getBoundingClientRect(), p = evtXY(e);
		ptr.x = ((p.x - r.left) / r.width) * 2 - 1;
		ptr.y = -((p.y - r.top) / r.height) * 2 + 1;
		return p;
	}

	function pickHit() {
		raycaster.setFromCamera(ptr, camera);
		var hits = raycaster.intersectObject(cube);
		if (!hits.length || !hits[0].face) return null;
		var slot = hits[0].face.materialIndex;
		if (typeof slot !== 'number' || slot < 0 || slot > 5) return null;
		return {
			faceIdx: SLOT_TO_FACE[slot],
			uv: hits[0].uv || null
		};
	}

	function onDown(e) {
		var p = setPtr(e);
		isDrag = true; didDrag = false;
		lx = p.x; ly = p.y; vx = 0; vy = 0;
		if (canvas.setPointerCapture && e.pointerId != null) {
			try { canvas.setPointerCapture(e.pointerId); } catch (_) {}
		}
	}

	function onMove(e) {
		var p = setPtr(e);
		if (isDrag && !ogcReducedMotion()) {
			var dx = p.x - lx, dy = p.y - ly;
			if (Math.abs(dx) + Math.abs(dy) > 5) didDrag = true;
			vx = dx * 0.012; vy = dy * 0.012;
			cube.rotation.y += vx; cube.rotation.x += vy;
			lx = p.x; ly = p.y;
			if (hintEl) hintEl.classList.add('is-hidden');
		} else if (!isDrag && (e.pointerType === 'mouse' || e.type === 'mousemove')) {
			var hit = pickHit();
			/* Pointer only over the baked CTA UV region on a revealed face */
			canvas.style.cursor =
				(hit && expanded === hit.faceIdx && hitButton(hit.uv)) ? 'pointer' : 'grab';
		}
	}

	function onUp(e) {
		if (!isDrag) return;
		isDrag = false;
		canvas.style.cursor = 'grab';
		if (didDrag) return;

		setPtr(e);
		var hit = pickHit();
		if (!hit) return;

		/* Click on baked button of an already-revealed face → navigate */
		if (expanded === hit.faceIdx && hitButton(hit.uv)) {
			window.location.href = absUrl(FACES[hit.faceIdx].url);
			return;
		}

		/* Otherwise toggle reveal / collapse */
		setExpanded(hit.faceIdx);
	}

	function frame() {
		if (!isDrag && !ogcReducedMotion()) {
			cube.rotation.y += 0.004 + vx;
			cube.rotation.x += 0.0015 + vy;
		}
		vx *= 0.92; vy *= 0.92;
		if (Math.abs(vx) < 0.0001) vx = 0;
		if (Math.abs(vy) < 0.0001) vy = 0;
		renderer.render(scene, camera);
	}

	function stop() { if (animId) { cancelAnimationFrame(animId); animId = 0; } }
	function tick() { animId = requestAnimationFrame(tick); frame(); }

	function sync() {
		stop();
		if (!inView) { frame(); return; }
		if (ogcReducedMotion()) { cube.rotation.set(0.38, -0.62, 0.1); vx = 0; vy = 0; frame(); return; }
		tick();
	}

	function resize() {
		var w = root.clientWidth || 320, h = root.clientHeight || 320;
		camera.aspect = w / h;
		camera.updateProjectionMatrix();
		renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
		renderer.setSize(w, h, false);
	}

	function init() {
		renderer = new THREE.WebGLRenderer({ canvas: canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
		renderer.setClearColor(0x000000, 0);

		scene  = new THREE.Scene();
		camera = new THREE.PerspectiveCamera(40, 1, 0.1, 40);
		camera.position.set(0, 0.2, 5.5);

		scene.add(new THREE.AmbientLight(0x5a7090, 0.30));
		var key  = new THREE.DirectionalLight(0xffb000, 1.4);  key.position.set(4.5, 5.5, 6);  scene.add(key);
		var fill = new THREE.DirectionalLight(0x6e8cae, 0.45); fill.position.set(-5, 1.5, 3);  scene.add(fill);
		var rim  = new THREE.DirectionalLight(0xffe8b3, 0.70); rim.position.set(-2.5, 4, -5); scene.add(rim);

		for (var i = 0; i < 6; i++) {
			titleTex[i]  = makeTex(FACES[i], false);
			revealTex[i] = makeTex(FACES[i], true);
		}

		faceMats = [];
		for (var s = 0; s < 6; s++) {
			var faceIdx = SLOT_TO_FACE[s];
			var m = new THREE.MeshPhysicalMaterial({
				map: titleTex[faceIdx],
				color: 0xffffff,
				roughness: 0.28,
				metalness: 0.12,
				clearcoat: 0.45,
				clearcoatRoughness: 0.18
			});
			if (m.clearCoat !== undefined) { m.clearCoat = 0.45; m.clearCoatRoughness = 0.18; }
			faceMats[s] = m;
		}

		cube = new THREE.Mesh(roundedBox(HALF * 2, HALF * 2, HALF * 2, 0.24, 4), faceMats);
		cube.rotation.set(0.38, -0.62, 0.1);
		scene.add(cube);

		resize();
		canvas.style.touchAction = 'none';
		canvas.style.cursor = 'grab';

		canvas.addEventListener('pointerdown', onDown);
		window.addEventListener('pointermove', onMove);
		window.addEventListener('pointerup', onUp);
		canvas.addEventListener('pointerleave', function () { canvas.style.cursor = 'grab'; });
		window.addEventListener('resize', function () { resize(); frame(); }, { passive: true });

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

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
