/**
 * OGC NewFinity — Three.js deep-space background (r128).
 * Brand accent: #FFB000 (0xFFB000). Responsive detail tiers + reduced-motion.
 */
(function () {
	'use strict';

	var BRAND = 0xffb000;
	var BG = 0x0a0a0a;

	var canvas = document.getElementById('ogc-space-canvas');
	var shell = document.querySelector('.ogc-bg-layer');
	if (!canvas || typeof THREE === 'undefined') {
		return;
	}

	var reduceMotionMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	var desktopMq = window.matchMedia('(min-width: 1025px)');
	var tabletMq = window.matchMedia('(min-width: 600px) and (max-width: 1024px)');

	var renderer;
	var scene;
	var camera;
	var asteroids = [];
	var particles;
	var particleVel;
	var particleBounds = 28;
	var animId = 0;
	var mouseX = 0;
	var mouseY = 0;
	var targetCamX = 0;
	var targetCamY = 0;
	var autoAngle = 0;
	var currentTier = '';
	var resizeTimer = 0;

	function getTier() {
		if (desktopMq.matches) {
			return 'desktop';
		}
		if (tabletMq.matches) {
			return 'tablet';
		}
		return 'mobile';
	}

	function tierConfig(tier) {
		if (tier === 'desktop') {
			return {
				particles: 280,
				asteroids: 7,
				detail: 1,
				mouseParallax: true,
				autoRotate: false
			};
		}
		if (tier === 'tablet') {
			return {
				particles: 140,
				asteroids: 4,
				detail: 0,
				mouseParallax: false,
				autoRotate: true
			};
		}
		return {
			particles: 90,
			asteroids: 3,
			detail: 0,
			mouseParallax: false,
			autoRotate: true
		};
	}

	function disposeObject3D(obj) {
		if (!obj) {
			return;
		}
		obj.traverse(function (child) {
			if (child.geometry) {
				child.geometry.dispose();
			}
			if (child.material) {
				if (Array.isArray(child.material)) {
					child.material.forEach(function (m) {
						m.dispose();
					});
				} else {
					child.material.dispose();
				}
			}
		});
	}

	function clearSceneContent() {
		asteroids.forEach(function (mesh) {
			scene.remove(mesh);
			disposeObject3D(mesh);
		});
		asteroids = [];

		if (particles) {
			scene.remove(particles);
			disposeObject3D(particles);
			particles = null;
			particleVel = null;
		}
	}

	function createAsteroid(detail) {
		var radius = 0.55 + Math.random() * 1.35;
		var geo = new THREE.IcosahedronGeometry(radius, detail);
		var pos = geo.attributes.position;
		var v = new THREE.Vector3();

		for (var i = 0; i < pos.count; i++) {
			v.fromBufferAttribute(pos, i);
			var noise = 0.82 + Math.random() * 0.38;
			v.multiplyScalar(noise);
			pos.setXYZ(i, v.x, v.y, v.z);
		}
		geo.computeVertexNormals();

		var mat = new THREE.MeshStandardMaterial({
			color: 0x1c1c1e,
			roughness: 0.88,
			metalness: 0.22,
			flatShading: true,
			emissive: BRAND,
			emissiveIntensity: 0.045
		});

		var mesh = new THREE.Mesh(geo, mat);
		mesh.position.set(
			(Math.random() - 0.5) * 36,
			(Math.random() - 0.5) * 22,
			(Math.random() - 0.5) * 28 - 6
		);
		mesh.rotation.set(Math.random() * Math.PI, Math.random() * Math.PI, Math.random() * Math.PI);
		mesh.userData.spin = {
			x: (Math.random() - 0.5) * 0.004,
			y: (Math.random() - 0.5) * 0.006,
			z: (Math.random() - 0.5) * 0.003
		};
		return mesh;
	}

	function createParticles(count) {
		var positions = new Float32Array(count * 3);
		particleVel = new Float32Array(count * 3);

		for (var i = 0; i < count; i++) {
			var i3 = i * 3;
			positions[i3] = (Math.random() - 0.5) * particleBounds * 2;
			positions[i3 + 1] = (Math.random() - 0.5) * particleBounds * 1.4;
			positions[i3 + 2] = (Math.random() - 0.5) * particleBounds * 2;
			particleVel[i3] = (Math.random() - 0.5) * 0.018;
			particleVel[i3 + 1] = (Math.random() - 0.5) * 0.014;
			particleVel[i3 + 2] = (Math.random() - 0.5) * 0.018;
		}

		var geo = new THREE.BufferGeometry();
		geo.setAttribute('position', new THREE.BufferAttribute(positions, 3));

		var mat = new THREE.PointsMaterial({
			color: BRAND,
			size: 0.08,
			transparent: true,
			opacity: 0.85,
			depthWrite: false,
			sizeAttenuation: true
		});

		return new THREE.Points(geo, mat);
	}

	function setupLights() {
		var ambient = new THREE.AmbientLight(0x404050, 0.55);
		scene.add(ambient);

		var key = new THREE.DirectionalLight(BRAND, 0.65);
		key.position.set(8, 12, 6);
		scene.add(key);

		var fill = new THREE.DirectionalLight(0x8899aa, 0.25);
		fill.position.set(-10, -4, -8);
		scene.add(fill);

		var glow = new THREE.PointLight(BRAND, 1.1, 48, 2);
		glow.position.set(-6, 4, 10);
		scene.add(glow);

		var glow2 = new THREE.PointLight(BRAND, 0.55, 40, 2);
		glow2.position.set(12, -6, -4);
		scene.add(glow2);
	}

	function buildContent(tier) {
		var cfg = tierConfig(tier);
		clearSceneContent();

		for (var a = 0; a < cfg.asteroids; a++) {
			var mesh = createAsteroid(cfg.detail);
			asteroids.push(mesh);
			scene.add(mesh);
		}

		particles = createParticles(cfg.particles);
		scene.add(particles);

		currentTier = tier;
		canvas.dataset.ogcTier = tier;
		applyHeroAsteroidFocus();
	}

	/**
	 * Homepage only: reuse one existing asteroid mesh, scale it up, and pin it
	 * to the hero's right-side viewport region (still spins via renderFrame).
	 */
	function applyHeroAsteroidFocus() {
		if (!document.body.classList.contains('ogc-has-hero-asteroid') || !asteroids.length) {
			return;
		}

		var hero = document.querySelector('[data-ogc-hero-focus], .ogc-home-hero');
		if (!hero) {
			return;
		}

		var mesh = asteroids[0];
		var rect = hero.getBoundingClientRect();
		var w = window.innerWidth;
		var h = window.innerHeight;
		var isMobile = w < 600;
		var isTablet = w >= 600 && w < 1025;

		// Target ~65–85% viewport width, vertically aligned with hero text block.
		var px = w * (isMobile ? 0.82 : 0.76);
		var py = Math.max(rect.top + rect.height * 0.42, h * 0.28);

		var ndcX = (px / w) * 2 - 1;
		var ndcY = -(py / h) * 2 + 1;
		var world = new THREE.Vector3(ndcX, ndcY, 0.45);
		world.unproject(camera);

		var dir = world.sub(camera.position).normalize();
		var distance = isMobile ? 10.5 : isTablet ? 11.5 : 12.2;
		mesh.position.copy(camera.position).add(dir.multiplyScalar(distance));

		var scale = isMobile ? 2.35 : isTablet ? 3.2 : 4.1;
		mesh.scale.setScalar(scale);
		mesh.userData.heroFocus = true;

		if (mesh.material) {
			mesh.material.emissiveIntensity = 0.12;
			mesh.material.metalness = 0.32;
			mesh.material.roughness = 0.78;
			mesh.material.needsUpdate = true;
		}
	}

	function resize() {
		var w = window.innerWidth;
		var h = window.innerHeight;
		camera.aspect = w / h;
		camera.updateProjectionMatrix();
		renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
		renderer.setSize(w, h, false);
		applyHeroAsteroidFocus();
	}

	function updateParticles() {
		if (!particles || !particleVel) {
			return;
		}
		var pos = particles.geometry.attributes.position.array;
		var n = pos.length / 3;
		var bx = particleBounds;
		var by = particleBounds * 0.7;
		var bz = particleBounds;

		for (var i = 0; i < n; i++) {
			var i3 = i * 3;
			pos[i3] += particleVel[i3];
			pos[i3 + 1] += particleVel[i3 + 1];
			pos[i3 + 2] += particleVel[i3 + 2];

			if (pos[i3] > bx || pos[i3] < -bx) {
				particleVel[i3] *= -1;
			}
			if (pos[i3 + 1] > by || pos[i3 + 1] < -by) {
				particleVel[i3 + 1] *= -1;
			}
			if (pos[i3 + 2] > bz || pos[i3 + 2] < -bz) {
				particleVel[i3 + 2] *= -1;
			}
		}
		particles.geometry.attributes.position.needsUpdate = true;
	}

	function renderFrame(animate) {
		var cfg = tierConfig(currentTier || getTier());

		if (animate && !reduceMotionMq.matches) {
			asteroids.forEach(function (mesh) {
				mesh.rotation.x += mesh.userData.spin.x;
				mesh.rotation.y += mesh.userData.spin.y;
				mesh.rotation.z += mesh.userData.spin.z;
			});
			updateParticles();

			if (cfg.mouseParallax) {
				targetCamX += (mouseX * 2.2 - targetCamX) * 0.04;
				targetCamY += (mouseY * 1.4 - targetCamY) * 0.04;
				camera.position.x = targetCamX;
				camera.position.y = 2 + targetCamY;
			} else if (cfg.autoRotate) {
				autoAngle += 0.0012;
				camera.position.x = Math.sin(autoAngle) * 2.4;
				camera.position.y = 2 + Math.cos(autoAngle * 0.7) * 0.6;
			}
			camera.lookAt(0, 0, 0);

			// Keep the homepage hero asteroid locked to the hero's right-side screen region
			// after camera parallax/auto-rotate updates.
			if (asteroids.length && asteroids[0].userData.heroFocus) {
				applyHeroAsteroidFocus();
			}
		}

		renderer.render(scene, camera);
	}

	function stopLoop() {
		if (animId) {
			cancelAnimationFrame(animId);
			animId = 0;
		}
	}

	function loop() {
		animId = requestAnimationFrame(loop);
		renderFrame(true);
	}

	function applyMotionPreference() {
		stopLoop();
		if (reduceMotionMq.matches) {
			renderFrame(false);
			return;
		}
		loop();
	}

	function onTierChange() {
		var tier = getTier();
		if (tier === currentTier) {
			resize();
			return;
		}
		buildContent(tier);
		resize();
		if (reduceMotionMq.matches) {
			renderFrame(false);
		}
	}

	function init() {
		renderer = new THREE.WebGLRenderer({
			canvas: canvas,
			antialias: getTier() === 'desktop',
			alpha: false,
			powerPreference: 'high-performance'
		});
		renderer.setClearColor(BG, 1);
		renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));

		scene = new THREE.Scene();
		scene.fog = new THREE.FogExp2(BG, 0.018);

		camera = new THREE.PerspectiveCamera(55, window.innerWidth / window.innerHeight, 0.1, 120);
		camera.position.set(0, 2, 16);

		setupLights();
		buildContent(getTier());
		resize();

		if (shell) {
			shell.classList.add('is-ready');
		}

		window.addEventListener(
			'mousemove',
			function (e) {
				if (!tierConfig(currentTier).mouseParallax || reduceMotionMq.matches) {
					return;
				}
				mouseX = (e.clientX / window.innerWidth) * 2 - 1;
				mouseY = -(e.clientY / window.innerHeight) * 2 + 1;
			},
			{ passive: true }
		);

		window.addEventListener(
			'resize',
			function () {
				clearTimeout(resizeTimer);
				resizeTimer = setTimeout(function () {
					onTierChange();
					resize();
				}, 150);
			},
			{ passive: true }
		);

		window.addEventListener(
			'orientationchange',
			function () {
				setTimeout(function () {
					onTierChange();
					resize();
				}, 200);
			},
			{ passive: true }
		);

		function bindMq(mq) {
			if (typeof mq.addEventListener === 'function') {
				mq.addEventListener('change', onTierChange);
			} else if (typeof mq.addListener === 'function') {
				mq.addListener(onTierChange);
			}
		}
		bindMq(desktopMq);
		bindMq(tabletMq);
		bindMq(reduceMotionMq);

		if (typeof reduceMotionMq.addEventListener === 'function') {
			reduceMotionMq.addEventListener('change', applyMotionPreference);
		} else if (typeof reduceMotionMq.addListener === 'function') {
			reduceMotionMq.addListener(applyMotionPreference);
		}

		applyMotionPreference();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
