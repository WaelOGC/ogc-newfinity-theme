/**
 * OGC NewFinity — ripple effect for .ogc-btn (Theme Options → Buttons & Effects).
 * Active when body has ogc-btn-primary-ripple or ogc-btn-outline-ripple.
 */
(function () {
	'use strict';

	var body = document.body;
	if (!body) {
		return;
	}

	var primaryRipple = body.classList.contains('ogc-btn-primary-ripple');
	var outlineRipple = body.classList.contains('ogc-btn-outline-ripple');

	if (!primaryRipple && !outlineRipple) {
		return;
	}

	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	if (body.classList.contains('ogc-motion-reduced-by-admin')) {
		return;
	}

	function isRippleTarget(button) {
		if (!button || !button.classList.contains('ogc-btn')) {
			return false;
		}
		var isOutline = button.classList.contains('ogc-btn-outline');
		return isOutline ? outlineRipple : primaryRipple;
	}

	function spawnRipple(button, event) {
		var rect = button.getBoundingClientRect();
		var size = Math.max(rect.width, rect.height) * 2;
		var x = event.clientX - rect.left - size / 2;
		var y = event.clientY - rect.top - size / 2;

		var ripple = document.createElement('span');
		ripple.className = 'ogc-ripple';
		ripple.style.width = size + 'px';
		ripple.style.height = size + 'px';
		ripple.style.left = x + 'px';
		ripple.style.top = y + 'px';

		button.appendChild(ripple);

		ripple.addEventListener('animationend', function () {
			ripple.remove();
		});
	}

	document.addEventListener(
		'pointerdown',
		function (event) {
			var button = event.target.closest('.ogc-btn');
			if (!isRippleTarget(button)) {
				return;
			}
			spawnRipple(button, event);
		},
		{ passive: true }
	);
})();
