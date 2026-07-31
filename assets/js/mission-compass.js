/**
 * OGC NewFinity — Our Mission hero 3D compass (page-specific).
 * Geometry-only dial + two-tone needle. Search → settle north → hold → repeat.
 * MeshPhysicalMaterial + cube studio lighting. No baked textures.
 */
(function () {
	'use strict';

	if (typeof THREE === 'undefined') { return; }

	var rmMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	function ogcReducedMotion() {
		return rmMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}

	/* Needle angles (rad around Z). 0 = north / +Y. */
	var NORTH = 0;
	var HOLD_MS = 2800;
	var SEARCH_MS = 2200;
	var SETTLE_MS = 1400;

	function makePhysical(color, metalness, clearcoat) {
		var mat = new THREE.MeshPhysicalMaterial({
			color: color,
			roughness: 0.28,
			metalness: metalness,
			clearcoat: clearcoat,
			clearcoatRoughness: 0.16
		});
		if (mat.clearCoat !== undefined) {
			mat.clearCoat = clearcoat;
			mat.clearCoatRoughness = 0.16;
		}
		return mat;
	}

	function easeOutCubic(t) {
		return 1 - Math.pow(1 - t, 3);
	}

	function initCompass(root) {
		var canvas = root.querySelector('canvas');
		if (!canvas) { return; }

		var renderer, scene, camera, compass, dial, needle;
		var animId = 0;
		var inView = true;
		var phase = 'search'; /* search | settle | hold */
		var phaseStart = 0;
		var searchFrom = 0;
		var searchTo = 0.55;
		var settleFrom = 0;

		function stop() {
			if (animId) {
				cancelAnimationFrame(animId);
				animId = 0;
			}
		}

		function setNeedle(angle) {
			if (needle) {
				needle.rotation.z = angle;
			}
		}

		function startSearch(now) {
			phase = 'search';
			phaseStart = now;
			searchFrom = needle ? needle.rotation.z : NORTH;
			/* Wander away from north, then back toward it during search */
			var dir = Math.random() > 0.5 ? 1 : -1;
			searchTo = searchFrom + dir * (0.45 + Math.random() * 0.55);
		}

		function startSettle(now) {
			phase = 'settle';
			phaseStart = now;
			settleFrom = needle ? needle.rotation.z : NORTH;
		}

		function startHold(now) {
			phase = 'hold';
			phaseStart = now;
			setNeedle(NORTH);
		}

		function frame(now) {
			now = now || performance.now();

			if (!ogcReducedMotion() && inView && needle) {
				var elapsed = now - phaseStart;

				if (phase === 'search') {
					var st = Math.min(1, elapsed / SEARCH_MS);
					var wobble = Math.sin(elapsed * 0.0045) * 0.12 * (1 - st * 0.4);
					var angled = searchFrom + (searchTo - searchFrom) * easeOutCubic(st);
					setNeedle(angled + wobble);
					if (st >= 1) {
						startSettle(now);
					}
				} else if (phase === 'settle') {
					var et = Math.min(1, elapsed / SETTLE_MS);
					var e = easeOutCubic(et);
					setNeedle(settleFrom + (NORTH - settleFrom) * e);
					if (et >= 1) {
						startHold(now);
					}
				} else {
					setNeedle(NORTH);
					if (elapsed >= HOLD_MS) {
						startSearch(now);
					}
				}

				if (dial) {
					dial.rotation.z += 0.00035;
				}
			}

			renderer.render(scene, camera);
		}

		function tick(now) {
			animId = requestAnimationFrame(tick);
			frame(now);
		}

		function sync() {
			stop();
			if (!inView) {
				frame();
				return;
			}
			if (ogcReducedMotion()) {
				setNeedle(NORTH);
				frame();
				return;
			}
			phaseStart = performance.now();
			if (phase === 'hold') {
				startHold(phaseStart);
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
		camera.position.set(0, 0.15, 4.4);

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

		compass = new THREE.Group();

		/* Dial plate — thin flat disc facing camera */
		var plate = new THREE.Mesh(
			new THREE.CylinderGeometry(1.05, 1.05, 0.07, 64),
			makePhysical(0x1a1a1c, 0.18, 0.4)
		);
		plate.rotation.x = Math.PI / 2;
		compass.add(plate);

		/* Outer rim */
		dial = new THREE.Mesh(
			new THREE.TorusGeometry(1.08, 0.055, 16, 80),
			makePhysical(0xffb000, 0.28, 0.5)
		);
		compass.add(dial);

		/* Inner tick ring */
		var inner = new THREE.Mesh(
			new THREE.TorusGeometry(0.72, 0.022, 12, 64),
			makePhysical(0xffd27a, 0.2, 0.45)
		);
		compass.add(inner);

		/* Center pivot */
		var pivot = new THREE.Mesh(
			new THREE.SphereGeometry(0.08, 24, 24),
			makePhysical(0xffe8b3, 0.35, 0.55)
		);
		pivot.position.z = 0.06;
		compass.add(pivot);

		/* Two-tone needle: warm amber north tip, cool pale south tip */
		needle = new THREE.Group();
		needle.position.z = 0.08;

		var northTip = new THREE.Mesh(
			new THREE.ConeGeometry(0.09, 0.95, 12),
			makePhysical(0xffb000, 0.3, 0.5)
		);
		northTip.position.y = 0.42;
		needle.add(northTip);

		var southTip = new THREE.Mesh(
			new THREE.ConeGeometry(0.09, 0.85, 12),
			makePhysical(0xc8d4e4, 0.22, 0.45)
		);
		southTip.rotation.z = Math.PI;
		southTip.position.y = -0.38;
		needle.add(southTip);

		compass.add(needle);
		compass.rotation.set(0.18, -0.22, 0);
		scene.add(compass);

		/* No ground plane — transparent canvas only */

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

		startHold(performance.now());
		sync();
	}

	function boot() {
		var roots = document.querySelectorAll('[data-ogc-mission-compass]');
		for (var i = 0; i < roots.length; i++) {
			initCompass(roots[i]);
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})();
