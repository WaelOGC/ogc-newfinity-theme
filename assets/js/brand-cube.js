/**
 * OGC NewFinity — interactive homepage brand cube (separate Three.js scene).
 * Drag to rotate; click/tap a face to reveal copy + link in place. No AI/chat logic.
 */
(function () {
	'use strict';

	var root = document.getElementById('ogc-brand-cube');
	var canvas = document.getElementById('ogc-brand-cube-canvas');
	if (!root || !canvas || typeof THREE === 'undefined') {
		return;
	}

	var reduceMotionMq = window.matchMedia('(prefers-reduced-motion: reduce)');
	var faceEls = Array.prototype.slice.call(root.querySelectorAll('.ogc-brand-cube__face'));
	var hintEl = root.querySelector('[data-cube-hint]');

	var FACE_NORMALS = [
		new THREE.Vector3(0, 0, 1), // About us — front
		new THREE.Vector3(0, 0, -1), // Our services — back
		new THREE.Vector3(0, 1, 0), // Our mission — top
		new THREE.Vector3(1, 0, 0), // Contact us — right
		new THREE.Vector3(-1, 0, 0), // Leave feedback — left
		new THREE.Vector3(0, -1, 0) // AI support — bottom
	];

	var renderer;
	var scene;
	var camera;
	var cube;
	var raycaster = new THREE.Raycaster();
	var pointer = new THREE.Vector2();
	var animId = 0;
	var inView = true;
	var isDragging = false;
	var didDrag = false;
	var lastX = 0;
	var lastY = 0;
	var expandedIndex = -1;
	var hoverIndex = -1;
	var half = 1.05;
	var tmpWorld = new THREE.Vector3();
	var tmpNormal = new THREE.Vector3();
	var camDir = new THREE.Vector3();
	var DRAG_THRESHOLD = 5;

	function createFaceMaterial() {
		return new THREE.MeshStandardMaterial({
			color: 0x1a1a1c,
			roughness: 0.48,
			metalness: 0.28,
			transparent: true,
			opacity: 0.92,
			emissive: 0xffb000,
			emissiveIntensity: 0.08
		});
	}

	function buildCube() {
		var geo = new THREE.BoxGeometry(half * 2, half * 2, half * 2);
		var mats = [
			createFaceMaterial(),
			createFaceMaterial(),
			createFaceMaterial(),
			createFaceMaterial(),
			createFaceMaterial(),
			createFaceMaterial()
		];
		cube = new THREE.Mesh(geo, mats);
		cube.userData.matToFace = [3, 4, 2, 5, 0, 1];
		scene.add(cube);

		var edges = new THREE.EdgesGeometry(geo);
		var line = new THREE.LineSegments(
			edges,
			new THREE.LineBasicMaterial({
				color: 0xffb000,
				transparent: true,
				opacity: 0.85
			})
		);
		cube.add(line);

		// Start slightly angled so the form reads as a cube, not a flat square.
		cube.rotation.set(0.42, -0.55, 0.12);
	}

	function resize() {
		var w = root.clientWidth || 320;
		var h = root.clientHeight || 320;
		camera.aspect = w / h;
		camera.updateProjectionMatrix();
		renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
		renderer.setSize(w, h, false);
	}

	function setPointerFromEvent(e) {
		var rect = canvas.getBoundingClientRect();
		var clientX = e.clientX;
		var clientY = e.clientY;
		if (e.touches && e.touches[0]) {
			clientX = e.touches[0].clientX;
			clientY = e.touches[0].clientY;
		} else if (e.changedTouches && e.changedTouches[0]) {
			clientX = e.changedTouches[0].clientX;
			clientY = e.changedTouches[0].clientY;
		}
		pointer.x = ((clientX - rect.left) / rect.width) * 2 - 1;
		pointer.y = -((clientY - rect.top) / rect.height) * 2 + 1;
		return { x: clientX, y: clientY };
	}

	function pickFaceIndex() {
		raycaster.setFromCamera(pointer, camera);
		var hits = raycaster.intersectObject(cube, false);
		if (!hits.length) {
			return -1;
		}
		var faceIndex = hits[0].face.materialIndex;
		return cube.userData.matToFace[faceIndex];
	}

	function hideHint() {
		if (hintEl) {
			hintEl.classList.add('is-hidden');
		}
	}

	function setExpanded(index) {
		if (expandedIndex === index) {
			expandedIndex = -1;
		} else {
			expandedIndex = index;
		}

		faceEls.forEach(function (el, i) {
			var open = i === expandedIndex;
			el.classList.toggle('is-expanded', open);
			el.setAttribute('aria-expanded', open ? 'true' : 'false');
		});
		hideHint();
	}

	function updateFaceOverlays() {
		if (!cube) {
			return;
		}
		cube.updateMatrixWorld(true);
		camera.getWorldDirection(camDir);
		var w = root.clientWidth;
		var h = root.clientHeight;

		FACE_NORMALS.forEach(function (normal, i) {
			var el = faceEls[i];
			if (!el) {
				return;
			}

			tmpNormal.copy(normal).transformDirection(cube.matrixWorld).normalize();
			var facing = -tmpNormal.dot(camDir);
			var visible = facing > 0.22;

			tmpWorld.copy(normal).multiplyScalar(half * 1.02);
			cube.localToWorld(tmpWorld);
			tmpWorld.project(camera);

			var x = (tmpWorld.x * 0.5 + 0.5) * w;
			var y = (-tmpWorld.y * 0.5 + 0.5) * h;
			var scale = 0.72 + Math.min(Math.max(facing, 0), 1) * 0.35;

			el.style.transform =
				'translate(-50%, -50%) translate(' + x.toFixed(1) + 'px,' + y.toFixed(1) + 'px) scale(' + scale.toFixed(3) + ')';
			el.style.opacity = visible ? String(0.45 + facing * 0.55) : '0';
			el.style.pointerEvents = visible ? 'auto' : 'none';
			el.classList.toggle('is-front', visible && facing > 0.55);
			el.classList.toggle('is-facing', visible);
			el.tabIndex = visible ? 0 : -1;
		});
	}

	function renderFrame() {
		if (!reduceMotionMq.matches && !isDragging) {
			cube.rotation.y += 0.0038;
			cube.rotation.x += 0.0016;
		}
		updateFaceOverlays();
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
		renderFrame();
	}

	function syncLoop() {
		stopLoop();
		if (!inView) {
			renderFrame();
			return;
		}
		if (reduceMotionMq.matches) {
			// Static default face orientation — About us toward camera.
			cube.rotation.set(0.18, -0.35, 0);
			renderFrame();
			return;
		}
		loop();
	}

	function onPointerDown(e) {
		if (reduceMotionMq.matches) {
			return;
		}
		var pt = setPointerFromEvent(e);
		isDragging = true;
		didDrag = false;
		lastX = pt.x;
		lastY = pt.y;
		root.classList.add('is-dragging');
	}

	function onPointerMove(e) {
		var pt = setPointerFromEvent(e);

		if (isDragging && !reduceMotionMq.matches) {
			var dx = pt.x - lastX;
			var dy = pt.y - lastY;
			if (Math.abs(dx) + Math.abs(dy) > DRAG_THRESHOLD) {
				didDrag = true;
			}
			cube.rotation.y += dx * 0.008;
			cube.rotation.x += dy * 0.008;
			lastX = pt.x;
			lastY = pt.y;
			hideHint();
			return;
		}

		// Hover affordance (desktop).
		if (e.pointerType === 'mouse' || e.type === 'mousemove') {
			var idx = pickFaceIndex();
			hoverIndex = idx;
			root.classList.toggle('is-face-hover', idx >= 0);
			faceEls.forEach(function (el, i) {
				el.classList.toggle('is-hover', i === idx);
			});
		}
	}

	function onPointerUp(e) {
		if (!isDragging && e.type.indexOf('touch') === -1 && e.type !== 'pointerup') {
			// keyboard / other
		}
		var wasDragging = isDragging;
		isDragging = false;
		root.classList.remove('is-dragging');

		if (!wasDragging) {
			return;
		}

		if (!didDrag) {
			setPointerFromEvent(e);
			var idx = pickFaceIndex();
			if (idx >= 0) {
				setExpanded(idx);
			}
		}
	}

	function bindFaceButtons() {
		faceEls.forEach(function (el, i) {
			el.addEventListener('click', function (e) {
				if (e.target.closest('a')) {
					return;
				}
				e.preventDefault();
				setExpanded(i);
			});

			el.addEventListener('keydown', function (e) {
				if (e.key === 'Enter' || e.key === ' ') {
					if (e.target.closest('a')) {
						return;
					}
					e.preventDefault();
					setExpanded(i);
				}
			});
		});
	}

	function init() {
		renderer = new THREE.WebGLRenderer({
			canvas: canvas,
			antialias: true,
			alpha: true,
			powerPreference: 'high-performance'
		});
		renderer.setClearColor(0x000000, 0);

		scene = new THREE.Scene();
		camera = new THREE.PerspectiveCamera(42, 1, 0.1, 40);
		camera.position.set(0, 0.15, 5.2);

		var ambient = new THREE.AmbientLight(0x707080, 0.85);
		scene.add(ambient);
		var key = new THREE.DirectionalLight(0xffb000, 1.05);
		key.position.set(3, 4, 5);
		scene.add(key);
		var fill = new THREE.DirectionalLight(0x88a0b8, 0.45);
		fill.position.set(-4, -2, 2);
		scene.add(fill);
		var rim = new THREE.DirectionalLight(0xffb000, 0.35);
		rim.position.set(-2, 1, -4);
		scene.add(rim);

		buildCube();
		resize();
		bindFaceButtons();

		if (reduceMotionMq.matches) {
			cube.rotation.set(0.35, -0.45, 0.08);
		}

		canvas.addEventListener('pointerdown', onPointerDown);
		window.addEventListener('pointermove', onPointerMove);
		window.addEventListener('pointerup', onPointerUp);
		canvas.addEventListener('pointerleave', function () {
			root.classList.remove('is-face-hover');
		});

		window.addEventListener(
			'resize',
			function () {
				resize();
				renderFrame();
			},
			{ passive: true }
		);

		if (typeof reduceMotionMq.addEventListener === 'function') {
			reduceMotionMq.addEventListener('change', syncLoop);
		} else if (typeof reduceMotionMq.addListener === 'function') {
			reduceMotionMq.addListener(syncLoop);
		}

		if ('IntersectionObserver' in window) {
			var io = new IntersectionObserver(
				function (entries) {
					inView = entries[0] && entries[0].isIntersecting;
					syncLoop();
				},
				{ threshold: 0.05 }
			);
			io.observe(root);
		}

		syncLoop();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
