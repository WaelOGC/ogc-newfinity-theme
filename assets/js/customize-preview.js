/**
 * Customizer live preview — OGC Hero Background essentials (postMessage).
 *
 * Image uploads use transport: refresh. Opacity, position, and overlay color
 * update CSS custom properties on :root (and ensure an overlay node exists)
 * without a full reload.
 */
(function ($) {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) {
		return;
	}

	var cfg = window.ogcHeroPreview || {};
	var pages = cfg.pages || ['home', 'about', 'services', 'mission'];
	var positions = cfg.positions || {};
	var root = document.documentElement;

	function setVar(name, value) {
		if (value === undefined || value === null) {
			return;
		}
		root.style.setProperty(name, String(value));
	}

	function ensureOverlay(page) {
		var layer = document.querySelector('.ogc-hero-bg--' + page);
		if (!layer) {
			return null;
		}
		var overlay = layer.querySelector('.ogc-hero-bg__overlay');
		if (!overlay) {
			overlay = document.createElement('span');
			overlay.className = 'ogc-hero-bg__overlay';
			layer.appendChild(overlay);
		}
		return overlay;
	}

	pages.forEach(function (page) {
		wp.customize('ogc_hero_bg_opacity_' + page, function (setting) {
			setting.bind(function (value) {
				var opacity = Math.max(0, Math.min(100, parseInt(value, 10) || 0)) / 100;
				setVar('--ogc-hero-bg-opacity-' + page, opacity);
			});
		});

		wp.customize('ogc_hero_bg_position_' + page, function (setting) {
			setting.bind(function (value) {
				var css = positions[value] || 'center center';
				setVar('--ogc-hero-bg-position-' + page, css);
			});
		});

		wp.customize('ogc_hero_bg_overlay_color_' + page, function (setting) {
			setting.bind(function (value) {
				var color = value || 'transparent';
				setVar('--ogc-hero-bg-overlay-color-' + page, color);
				if (value) {
					ensureOverlay(page);
				}
			});
		});
	});
}(jQuery));
