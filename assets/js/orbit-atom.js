/**
 * OGC NewFinity — About Us hero orbit/atom emblem.
 * Decorative only: glossy MeshPhysicalMaterial core + elliptical rings,
 * same studio lighting as the homepage brand cube. Auto-rotate; no interaction.
 */
(function () {
	'use strict';

	var root   = document.getElementById('ogc-orbit-atom');
	var canvas = document.getElementById('ogc-orbit-atom-canvas');
	if (!root || !canvas || typeof THREE === 'undefined') { return; }

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	var renderer, scene, camera, emblem;
	var animId = 0, inView = true;

	function stop() {
		if (animId) {
			cancelAnimationFrame(animId);
			animId = 0;
		}
	}

	function frame() {
		if (!rmMq.matches && emblem) {
			emblem.rotation.y += 0.005;
			emblem.rotation.x += 0.0012;
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
				emblem.rotation.set(0.35, 0.55, 0.12);
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

	function makeRing(radius, tube, color, rx, ry, rz) {
		var geo = new THREE.TorusGeometry(radius, tube, 24, 96);
		var mat = new THREE.MeshPhysicalMaterial({
			color: color,
			roughness: 0.22,
			metalness: 0.35,
			clearcoat: 0.55,
			clearcoatRoughness: 0.14,
			emissive: color,
			emissiveIntensity: 0.08
		});
		if (mat.clearCoat !== undefined) {
			mat.clearCoat = 0.55;
			mat.clearCoatRoughness = 0.14;
		}
		var mesh = new THREE.Mesh(geo, mat);
		mesh.rotation.set(rx, ry, rz);
		return mesh;
	}

	function init() {
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

		var coreMat = new THREE.MeshPhysicalMaterial({
			color: 0xffb000,
			roughness: 0.24,
			metalness: 0.28,
			clearcoat: 0.55,
			clearcoatRoughness: 0.16,
			emissive: 0xffb000,
			emissiveIntensity: 0.12
		});
		if (coreMat.clearCoat !== undefined) {
			coreMat.clearCoat = 0.55;
			coreMat.clearCoatRoughness = 0.16;
		}
		var core = new THREE.Mesh(new THREE.SphereGeometry(0.55, 48, 48), coreMat);
		emblem.add(core);

		/* Inner soft core highlight */
		var innerMat = new THREE.MeshPhysicalMaterial({
			color: 0xffe8b3,
			roughness: 0.18,
			metalness: 0.15,
			clearcoat: 0.6,
			clearcoatRoughness: 0.12,
			emissive: 0xffe8b3,
			emissiveIntensity: 0.2
		});
		if (innerMat.clearCoat !== undefined) {
			innerMat.clearCoat = 0.6;
			innerMat.clearCoatRoughness = 0.12;
		}
		emblem.add(new THREE.Mesh(new THREE.SphereGeometry(0.22, 32, 32), innerMat));

		/* Three elliptical orbital rings at different angles (logo atom motif) */
		emblem.add(makeRing(1.35, 0.045, 0xffb000, Math.PI / 2, 0, 0));
		emblem.add(makeRing(1.45, 0.038, 0xffd27a, Math.PI / 3.2, 0, Math.PI / 3.5));
		emblem.add(makeRing(1.55, 0.032, 0xffe8b3, -Math.PI / 3.4, 0, -Math.PI / 2.8));

		/* Subtle grounding glow disc */
		var glowGeo = new THREE.CircleGeometry(1.4, 48);
		var glowMat = new THREE.MeshBasicMaterial({
			color: 0xffb000,
			transparent: true,
			opacity: 0.14,
			depthWrite: false,
			side: THREE.DoubleSide
		});
		var glow = new THREE.Mesh(glowGeo, glowMat);
		glow.rotation.x = -Math.PI / 2;
		glow.position.y = -1.55;
		emblem.add(glow);

		emblem.rotation.set(0.35, 0.55, 0.12);
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

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
