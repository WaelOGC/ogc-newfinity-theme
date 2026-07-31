/**
 * OGC NewFinity — Three.js deep-space background (r128).
 * Brand accent: #FFB000 (0xFFB000). Responsive detail tiers + reduced-motion.
 * Organic rock asteroids: multi-octave noise silhouette + photo-texture surface.
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
	function ogcReducedMotion() {
		return reduceMotionMq.matches || document.body.classList.contains('ogc-motion-reduced-by-admin');
	}
	var desktopMq = window.matchMedia('(min-width: 1025px)');
	var tabletMq = window.matchMedia('(min-width: 600px) and (max-width: 1024px)');

	/* Shared photo textures — loaded once, reused across all asteroid instances */
	var rockTextures = [];
	(function loadRockTextures() {
		var base = (window.ogcSpaceBg && window.ogcSpaceBg.themeUri)
			? window.ogcSpaceBg.themeUri + '/assets/images/asteroids/'
			: '/wp-content/themes/ogc-newfinity/assets/images/asteroids/';
		var loader = new THREE.TextureLoader();
		var names = ['asteroid-01.webp', 'asteroid-02.webp', 'asteroid-03.webp'];
		names.forEach(function (name) {
			var t = loader.load(base + name);
			t.wrapS = t.wrapT = THREE.RepeatWrapping;
			if (THREE.sRGBEncoding !== undefined) {
				t.encoding = THREE.sRGBEncoding;
			}
			rockTextures.push(t);
		});
	}());

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
				detail: 2,
				mouseParallax: true,
				autoRotate: false
			};
		}
		if (tier === 'tablet') {
			return {
				particles: 140,
				asteroids: 4,
				detail: 1,
				mouseParallax: false,
				autoRotate: true
			};
		}
		return {
			particles: 90,
			asteroids: 3,
			detail: 1,
			mouseParallax: false,
			autoRotate: true
		};
	}

	/* ---- compact 3D value noise + two-octave fbm for rock displacement ---- */

	function fade(t) {
		return t * t * t * (t * (t * 6 - 15) + 10);
	}

	function lerp(a, b, t) {
		return a + (b - a) * t;
	}

	function hash3(ix, iy, iz) {
		var n = Math.sin(ix * 127.1 + iy * 311.7 + iz * 74.7 + 19.19) * 43758.5453123;
		return n - Math.floor(n);
	}

	function valueNoise3(x, y, z) {
		var x0 = Math.floor(x);
		var y0 = Math.floor(y);
		var z0 = Math.floor(z);
		var fx = fade(x - x0);
		var fy = fade(y - y0);
		var fz = fade(z - z0);

		var n000 = hash3(x0, y0, z0);
		var n100 = hash3(x0 + 1, y0, z0);
		var n010 = hash3(x0, y0 + 1, z0);
		var n110 = hash3(x0 + 1, y0 + 1, z0);
		var n001 = hash3(x0, y0, z0 + 1);
		var n101 = hash3(x0 + 1, y0, z0 + 1);
		var n011 = hash3(x0, y0 + 1, z0 + 1);
		var n111 = hash3(x0 + 1, y0 + 1, z0 + 1);

		var nx00 = lerp(n000, n100, fx);
		var nx10 = lerp(n010, n110, fx);
		var nx01 = lerp(n001, n101, fx);
		var nx11 = lerp(n011, n111, fx);
		var nxy0 = lerp(nx00, nx10, fy);
		var nxy1 = lerp(nx01, nx11, fy);
		return lerp(nxy0, nxy1, fz);
	}

	/** Large-scale bumps + fine surface roughness (two+ octaves). */
	function rockFbm(x, y, z, seed) {
		var s = seed || 0;
		var large = valueNoise3(x * 1.15 + s, y * 1.15, z * 1.15);
		var fine = valueNoise3(x * 4.2 + s * 2.1, y * 4.2 - s, z * 4.2 + 3.7);
		var micro = valueNoise3(x * 9.5 - s, y * 9.5 + s * 0.5, z * 9.5);
		return large * 0.62 + fine * 0.28 + micro * 0.1;
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

	/**
	 * Triplanar rock photo projection + amber fresnel rim (MeshStandardMaterial).
	 * Object-space position/normal — coverage independent of mesh UVs after noise displace.
	 */
	function applyRockMaterial(mat, rockScale) {
		mat.onBeforeCompile = function (shader) {
			shader.uniforms.uRimColor = { value: new THREE.Color(BRAND) };
			shader.uniforms.uRimPower = { value: 2.6 };
			shader.uniforms.uRimStrength = { value: 0.42 };
			shader.uniforms.uRockScale = { value: rockScale || 0.55 };

			shader.vertexShader =
				'varying vec3 vRockPos;\nvarying vec3 vRockNormal;\n' +
				shader.vertexShader;
			shader.vertexShader = shader.vertexShader.replace(
				'#include <begin_vertex>',
				[
					'#include <begin_vertex>',
					'vRockPos = position;',
					'vRockNormal = normal;'
				].join('\n')
			);

			shader.fragmentShader =
				'varying vec3 vRockPos;\nvarying vec3 vRockNormal;\n' +
				'uniform float uRockScale;\n' +
				'uniform vec3 uRimColor;\nuniform float uRimPower;\nuniform float uRimStrength;\n' +
				shader.fragmentShader;

			/* Triplanar projection — face normals from position derivatives (flat-shade safe) */
			shader.fragmentShader = shader.fragmentShader.replace(
				'#include <map_fragment>',
				[
					'#ifdef USE_MAP',
					'  vec3 rockN = abs( normalize( cross( dFdx( vRockPos ), dFdy( vRockPos ) ) ) );',
					'  if ( length( rockN ) < 1e-4 ) rockN = abs( normalize( vRockNormal ) );',
					'  rockN = pow( rockN, vec3( 2.5 ) );',
					'  rockN /= ( rockN.x + rockN.y + rockN.z + 1e-5 );',
					'  vec3 rockP = vRockPos * uRockScale;',
					'  vec4 mapX = texture2D( map, rockP.zy );',
					'  vec4 mapY = texture2D( map, rockP.xz );',
					'  vec4 mapZ = texture2D( map, rockP.xy );',
					'  vec4 texelColor = mapX * rockN.x + mapY * rockN.y + mapZ * rockN.z;',
					'  texelColor = mapTexelToLinear( texelColor );',
					'  diffuseColor *= texelColor;',
					'#endif'
				].join('\n')
			);

			shader.fragmentShader = shader.fragmentShader.replace(
				'#include <output_fragment>',
				[
					'{',
					'  float rimDot = abs( dot( normalize( normal ), normalize( vViewPosition ) ) );',
					'  float rim = pow( clamp( 1.0 - rimDot, 0.0, 1.0 ), uRimPower );',
					'  outgoingLight += uRimColor * rim * uRimStrength;',
					'}',
					'#include <output_fragment>'
				].join('\n')
			);
			mat.userData.shader = shader;
		};
		mat.needsUpdate = true;
	}

	function createAsteroid(detail) {
		var radius = 0.55 + Math.random() * 1.35;
		var seed = Math.random() * 40;
		var geo = new THREE.IcosahedronGeometry(radius, detail);
		var pos = geo.attributes.position;
		var v = new THREE.Vector3();

		for (var i = 0; i < pos.count; i++) {
			v.fromBufferAttribute(pos, i);
			var len = v.length() || 1;
			v.multiplyScalar(1 / len);
			var n = rockFbm(v.x * 1.4, v.y * 1.4, v.z * 1.4, seed);
			var r = radius * (0.76 + n * 0.52);
			pos.setXYZ(i, v.x * r, v.y * r, v.z * r);
		}
		geo.computeVertexNormals();

		/* Pick one of the three rock photo textures at random */
		var tex = rockTextures.length
			? rockTextures[Math.floor(Math.random() * rockTextures.length)]
			: null;

		var mat = new THREE.MeshStandardMaterial({
			color:             tex ? 0xffffff : 0x1a1a1c,
			map:               tex || null,
			roughness:         0.92,
			metalness:         0.16,
			flatShading:       true,
			emissive:          BRAND,
			emissiveIntensity: 0.028
		});
		/* Scale so each rock gets ~2–3 texture repeats across its diameter */
		applyRockMaterial(mat, 0.85 / Math.max(radius, 0.4));

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
		var ambient = new THREE.AmbientLight(0x404050, 0.5);
		scene.add(ambient);

		var key = new THREE.DirectionalLight(BRAND, 0.7);
		key.position.set(8, 12, 6);
		scene.add(key);

		var fill = new THREE.DirectionalLight(0x8899aa, 0.28);
		fill.position.set(-10, -4, -8);
		scene.add(fill);

		// Rim / silhouette lift against deep space (amber family).
		var rim = new THREE.DirectionalLight(BRAND, 0.38);
		rim.position.set(-5, 3, 14);
		scene.add(rim);

		var glow = new THREE.PointLight(BRAND, 1.05, 48, 2);
		glow.position.set(-6, 4, 10);
		scene.add(glow);

		var glow2 = new THREE.PointLight(BRAND, 0.5, 40, 2);
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
	}

	function resize() {
		var w = window.innerWidth;
		var h = window.innerHeight;
		camera.aspect = w / h;
		camera.updateProjectionMatrix();
		renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
		renderer.setSize(w, h, false);
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

		if (animate && !ogcReducedMotion()) {
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
		if (ogcReducedMotion()) {
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
		if (ogcReducedMotion()) {
			renderFrame(false);
		}
	}

	function init() {
		renderer = new THREE.WebGLRenderer({
			canvas: canvas,
			antialias: getTier() === 'desktop',
			alpha: true,
			powerPreference: 'high-performance'
		});
		/* Transparent clear so the CSS nebula layer shows through empty space only */
		renderer.setClearColor(BG, 0);
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
				if (!tierConfig(currentTier).mouseParallax || ogcReducedMotion()) {
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
