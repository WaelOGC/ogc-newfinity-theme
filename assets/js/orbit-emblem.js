/**
 * OGC NewFinity — shared hero orbit/atom emblem.
 * Decorative only: OGC-baked sphere + elliptical rings.
 * MeshPhysicalMaterial + cube studio lighting. Y-axis auto-rotate; no interaction.
 *
 * Drop-in: include template-parts/hero-orbit-atom.php (or any
 * [data-ogc-orbit-emblem] root with a child canvas). One script initializes all.
 */
(function () {
	'use strict';

	if (typeof THREE === 'undefined') { return; }

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	var TEX = 1024;

	/* Fixed tilt so rings read at an angle; spin is Y-only so OGC stays readable. */
	var TILT_X = 0.28;
	var TILT_Z = 0.08;
	var FACE_Y = 0;

	function makeOgcTexture() {
		var c = document.createElement('canvas');
		c.width = c.height = TEX;
		var ctx = c.getContext('2d');

		ctx.fillStyle = '#161618';
		ctx.fillRect(0, 0, TEX, TEX);

		var g = ctx.createRadialGradient(
			TEX * 0.5, TEX * 0.4, TEX * 0.04,
			TEX * 0.5, TEX * 0.5, TEX * 0.72
		);
		g.addColorStop(0, 'rgba(255,176,0,0.16)');
		g.addColorStop(0.45, 'rgba(22,22,24,0.9)');
		g.addColorStop(1, 'rgba(12,12,14,1)');
		ctx.fillStyle = g;
		ctx.fillRect(0, 0, TEX, TEX);

		var word = document.createElement('canvas');
		word.width = 360;
		word.height = 160;
		var wctx = word.getContext('2d');
		wctx.fillStyle = '#FFB000';
		wctx.font = 'bold 112px "Space Grotesk", Inter, sans-serif';
		wctx.textAlign = 'center';
		wctx.textBaseline = 'middle';
		wctx.shadowColor = 'rgba(255,176,0,0.35)';
		wctx.shadowBlur = 14;
		wctx.fillText('OGC', word.width / 2, word.height / 2);

		var wy = TEX * 0.52 - word.height / 2;
		ctx.drawImage(word, TEX * 0.5 - word.width / 2, wy);

		var half = word.width / 2;
		ctx.drawImage(word, half, 0, half, word.height, 0, wy, half, word.height);
		ctx.drawImage(word, 0, 0, half, word.height, TEX - half, wy, half, word.height);

		var t = new THREE.CanvasTexture(c);
		t.anisotropy = 4;
		t.needsUpdate = true;
		return t;
	}

	function makeEnvMap() {
		var size = 64;
		var faces = [];
		var colors = [
			[255, 176, 0],
			[110, 140, 174],
			[255, 232, 179],
			[20, 20, 24],
			[255, 200, 80],
			[40, 44, 56]
		];
		for (var i = 0; i < 6; i++) {
			var cv = document.createElement('canvas');
			cv.width = cv.height = size;
			var cx = cv.getContext('2d');
			var col = colors[i];
			var grd = cx.createRadialGradient(size * 0.35, size * 0.3, 2, size * 0.5, size * 0.5, size * 0.7);
			grd.addColorStop(0, 'rgb(' + Math.min(255, col[0] + 40) + ',' + Math.min(255, col[1] + 40) + ',' + Math.min(255, col[2] + 40) + ')');
			grd.addColorStop(1, 'rgb(' + col[0] + ',' + col[1] + ',' + col[2] + ')');
			cx.fillStyle = grd;
			cx.fillRect(0, 0, size, size);
			faces.push(cv);
		}
		var env = new THREE.CubeTexture(faces);
		env.needsUpdate = true;
		return env;
	}

	function makeRing(radius, tube, color, metalness, rx, ry, rz, envMap) {
		var geo = new THREE.TorusGeometry(radius, tube, 48, 160);
		var mat = new THREE.MeshPhysicalMaterial({
			color: color,
			roughness: 0.26,
			metalness: metalness,
			clearcoat: 0.55,
			clearcoatRoughness: 0.14,
			envMap: envMap,
			envMapIntensity: 1.15
		});
		if (mat.clearCoat !== undefined) {
			mat.clearCoat = 0.55;
			mat.clearCoatRoughness = 0.14;
		}
		var mesh = new THREE.Mesh(geo, mat);
		mesh.rotation.set(rx, ry, rz);
		return mesh;
	}

	function initEmblem(root) {
		var canvas = root.querySelector('canvas');
		if (!canvas) { return; }

		var renderer, scene, camera, emblem;
		var animId = 0;
		var inView = true;

		function stop() {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = 0;
			}
		}

		function frame() {
			if (!rmMq.matches && emblem) {
				emblem.rotation.y += 0.006;
			}
			renderer.render(scene, camera);
		}

		function tick() {
			animId = requestAnimationFrame(tick);
			frame();
		}

		function sync() {
			stop();
			if (!inView) {
				frame();
				return;
			}
			if (rmMq.matches) {
				if (emblem) {
					emblem.rotation.set(TILT_X, FACE_Y, TILT_Z);
				}
				frame();
				return;
			}
			tick();
		}

		function resize() {
			var w = root.clientWidth || 320;
			var h = root.clientHeight || 320;
			camera.aspect = w / h;
			camera.updateProjectionMatrix();
			renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
			renderer.setSize(w, h, false);
		}

		renderer = new THREE.WebGLRenderer({
			canvas: canvas,
			antialias: true,
			alpha: true,
			powerPreference: 'high-performance'
		});
		renderer.setClearColor(0x000000, 0);

		scene  = new THREE.Scene();
		camera = new THREE.PerspectiveCamera(40, 1, 0.1, 40);
		camera.position.set(0, 0.15, 5.2);

		/* Same 3-point studio setup as brand-cube.js */
		scene.add(new THREE.AmbientLight(0x5a7090, 0.30));
		var key  = new THREE.DirectionalLight(0xffb000, 1.4);
		key.position.set(4.5, 5.5, 6);
		scene.add(key);
		var fill = new THREE.DirectionalLight(0x6e8cae, 0.45);
		fill.position.set(-5, 1.5, 3);
		scene.add(fill);
		var rim  = new THREE.DirectionalLight(0xffe8b3, 0.70);
		rim.position.set(-2.5, 4, -5);
		scene.add(rim);

		emblem = new THREE.Group();

		var envMap = makeEnvMap();
		var ogcMap = makeOgcTexture();
		var coreMat = new THREE.MeshPhysicalMaterial({
			map: ogcMap,
			color: 0xffffff,
			roughness: 0.28,
			metalness: 0.12,
			clearcoat: 0.35,
			clearcoatRoughness: 0.2,
			envMap: envMap,
			envMapIntensity: 0.45
		});
		if (coreMat.clearCoat !== undefined) {
			coreMat.clearCoat = 0.35;
			coreMat.clearCoatRoughness = 0.2;
		}
		emblem.add(new THREE.Mesh(new THREE.SphereGeometry(0.58, 64, 64), coreMat));

		emblem.add(makeRing(1.35, 0.055, 0xffb000, 0.28, Math.PI / 2, 0, 0, envMap));
		emblem.add(makeRing(1.48, 0.046, 0xffd27a, 0.20, Math.PI / 3.2, 0, Math.PI / 3.5, envMap));
		emblem.add(makeRing(1.60, 0.038, 0xffe8b3, 0.14, -Math.PI / 3.4, 0, -Math.PI / 2.8, envMap));

		emblem.rotation.set(TILT_X, FACE_Y, TILT_Z);
		scene.add(emblem);

		resize();
		canvas.style.pointerEvents = 'none';

		window.addEventListener('resize', function () {
			resize();
			frame();
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
		var roots = document.querySelectorAll('[data-ogc-orbit-emblem]');
		for (var i = 0; i < roots.length; i++) {
			initEmblem(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
