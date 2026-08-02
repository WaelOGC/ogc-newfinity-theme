/**
 * Customizer live preview — OGC theme options (postMessage).
 *
 * Same theme_mod keys as the dashboard. Colors, ranges, selects, numbers, and
 * text update CSS custom properties / body classes / DOM text instantly.
 * Image uploads (and JSON columns / font families) use transport: refresh.
 */
(function ($) {
	'use strict';

	if (typeof wp === 'undefined' || !wp.customize) {
		return;
	}

	var cfg = window.ogcHeroPreview || {};
	var speeds = cfg.speeds || { fast: 120, normal: 220, slow: 380 };
	var transitions = cfg.transitions || { fast: 150, normal: 250, slow: 400 };
	var root = document.documentElement;
	var body = document.body;

	var state = {
		accentStart: null,
		accentEnd: null,
		btnStart: null,
		btnEnd: null,
		angle: null,
		shadowHex: null,
		shadowOpacity: null,
		brandRgb: null,
		headerHex: null,
		headerOpacity: null
	};

	function setVar(name, value) {
		if (value === undefined || value === null) {
			return;
		}
		root.style.setProperty(name, String(value));
	}

	function clamp(n, min, max) {
		return Math.max(min, Math.min(max, n));
	}

	function hexToRgb(hex) {
		if (!hex || typeof hex !== 'string') {
			return '0, 0, 0';
		}
		var h = hex.replace('#', '').trim();
		if (h.length === 3) {
			h = h[0] + h[0] + h[1] + h[1] + h[2] + h[2];
		}
		if (h.length !== 6) {
			return '0, 0, 0';
		}
		return [
			parseInt(h.slice(0, 2), 16),
			parseInt(h.slice(2, 4), 16),
			parseInt(h.slice(4, 6), 16)
		].join(', ');
	}

	function speedMs(token, global) {
		var map = global ? transitions : speeds;
		return map[token] || map.normal || 250;
	}

	function intensityValues(token) {
		var scale = {
			subtle: { lift: '-2px', scale: '1.01', glow: '0.25' },
			medium: { lift: '-4px', scale: '1.03', glow: '0.4' },
			strong: { lift: '-7px', scale: '1.06', glow: '0.6' }
		};
		return scale[token] || scale.medium;
	}

	function buttonShadow(style, shadowRgb, brandRgb, opacity) {
		switch (style) {
			case 'none':
				return 'none';
			case 'glow':
				return '0 0 24px rgba(' + brandRgb + ', ' + Math.min(1, opacity + 0.1) + ')';
			case 'hard':
				return '0 4px 0 rgba(' + shadowRgb + ', ' + opacity + ')';
			case 'soft':
			default:
				return '0 8px 24px rgba(' + shadowRgb + ', ' + opacity + ')';
		}
	}

	function updateAccentGradient() {
		if (!state.accentStart || !state.accentEnd || state.angle === null) {
			return;
		}
		setVar('--accent-gradient-start', state.accentStart);
		setVar('--accent-gradient-end', state.accentEnd);
		setVar('--ogc-link-color', state.accentStart);
		setVar('--ogc-link-hover', state.accentEnd);
		setVar('--ogc-focus-ring', state.accentEnd);
		setVar('--ogc-gradient-angle', state.angle + 'deg');
		setVar(
			'--ogc-accent-gradient',
			'linear-gradient(' + state.angle + 'deg, ' + state.accentStart + ' 0%, ' + state.accentEnd + ' 100%)'
		);
		state.brandRgb = hexToRgb(state.accentStart);
		setVar('--ogc-brand-rgb', state.brandRgb);
		setVar('--ogc-brand-hover-rgb', hexToRgb(state.accentEnd));
		setVar('--ogc-btn-hover-bg', 'rgba(' + state.brandRgb + ', 0.16)');
		updateButtonGradient();
		updateShadowVars(null);
	}

	function updateButtonGradient() {
		if (!state.btnStart || !state.btnEnd || state.angle === null) {
			return;
		}
		setVar('--ogc-btn-bg-start', state.btnStart);
		setVar('--ogc-btn-bg-end', state.btnEnd);
		setVar(
			'--ogc-btn-gradient',
			'linear-gradient(' + state.angle + 'deg, ' + state.btnStart + ' 0%, ' + state.btnEnd + ' 100%)'
		);
	}

	function updateShadowVars(style) {
		if (!state.shadowHex || state.shadowOpacity === null) {
			return;
		}
		var rgb = hexToRgb(state.shadowHex);
		var op = clamp(parseInt(state.shadowOpacity, 10) || 0, 0, 100) / 100;
		setVar('--ogc-shadow-rgb', rgb);
		setVar('--ogc-shadow-opacity', String(op));
		setVar('--ogc-shadow-color', 'rgba(' + rgb + ', ' + op + ')');
		if (style) {
			setVar('--ogc-btn-shadow', buttonShadow(style, rgb, state.brandRgb || hexToRgb(state.accentStart), op));
		} else if (wp.customize('ogc_button_shadow_style')) {
			setVar(
				'--ogc-btn-shadow',
				buttonShadow(
					wp.customize('ogc_button_shadow_style').get(),
					rgb,
					state.brandRgb || hexToRgb(state.accentStart),
					op
				)
			);
		}
	}

	function updateHeaderBg() {
		if (!state.headerHex || state.headerOpacity === null) {
			return;
		}
		var rgb = hexToRgb(state.headerHex);
		var op = clamp(parseInt(state.headerOpacity, 10) || 0, 0, 100) / 100;
		setVar('--ogc-header-bg', 'rgba(' + rgb + ', ' + op + ')');
		setVar('--ogc-header-bg-scrolled', 'rgba(' + rgb + ', ' + Math.max(0.5, op) + ')');
	}

	function replaceBodyClass(prefix, value) {
		var list = body.className.split(/\s+/).filter(Boolean);
		var next = [];
		for (var i = 0; i < list.length; i++) {
			if (list[i].indexOf(prefix) !== 0) {
				next.push(list[i]);
			}
		}
		if (value !== null && value !== undefined && value !== '') {
			next.push(prefix + String(value).replace(/[^a-zA-Z0-9_-]/g, ''));
		}
		body.className = next.join(' ');
	}

	function bind(settingId, fn) {
		wp.customize(settingId, function (setting) {
			setting.bind(fn);
			fn(setting.get());
		});
	}

	/* ---------------------------------------------------------------- Header */
	bind('ogc_header_bg_color', function (value) {
		state.headerHex = value;
		updateHeaderBg();
	});

	bind('ogc_header_bg_opacity', function (value) {
		state.headerOpacity = value;
		updateHeaderBg();
	});

	bind('ogc_header_text_color', function (value) {
		setVar('--ogc-header-text-color', value);
	});

	bind('ogc_header_logo_width_desktop', function (value) {
		var px = clamp(parseInt(value, 10) || 54, 16, 320) + 'px';
		setVar('--ogc-header-logo-width', px);
		setVar('--ogc-header-logo-width-desktop', px);
	});

	bind('ogc_header_logo_width_mobile', function (value) {
		setVar('--ogc-header-logo-width-mobile', clamp(parseInt(value, 10) || 40, 16, 320) + 'px');
	});

	bind('ogc_header_sticky', function (value) {
		var on = !!value && value !== 'false' && value !== '0';
		body.classList.toggle('ogc-header-sticky', on);
		body.classList.toggle('ogc-header-static', !on);
	});

	/* ---------------------------------------------------------------- Footer */
	bind('ogc_footer_bg_color', function (value) {
		setVar('--ogc-footer-bg', value);
	});

	bind('ogc_footer_logo_width', function (value) {
		setVar('--ogc-footer-logo-width', clamp(parseInt(value, 10) || 54, 16, 320) + 'px');
	});

	bind('ogc_footer_tagline', function (value) {
		var el = document.querySelector('.ogc-footer__about');
		if (el) {
			el.textContent = value || '';
		}
	});

	/* ---------------------------------------------------------------- Colors */
	bind('ogc_color_accent_start', function (value) {
		state.accentStart = value;
		updateAccentGradient();
	});

	bind('ogc_color_accent_end', function (value) {
		state.accentEnd = value;
		updateAccentGradient();
	});

	bind('ogc_color_bg', function (value) {
		setVar('--bg-primary', value);
	});

	bind('ogc_color_text_primary', function (value) {
		setVar('--text-primary', value);
		setVar('--ogc-text-primary', value);
	});

	bind('ogc_color_text_secondary', function (value) {
		setVar('--text-secondary', value);
	});

	bind('ogc_color_border', function (value) {
		setVar('--border-accent', value);
	});

	bind('ogc_color_button_bg_start', function (value) {
		state.btnStart = value;
		updateButtonGradient();
	});

	bind('ogc_color_button_bg_end', function (value) {
		state.btnEnd = value;
		updateButtonGradient();
	});

	bind('ogc_color_button_text', function (value) {
		setVar('--ogc-btn-text', value);
	});

	bind('ogc_gradient_angle', function (value) {
		state.angle = clamp(parseInt(value, 10) || 135, 0, 360);
		updateAccentGradient();
		updateButtonGradient();
	});

	bind('ogc_color_shadow', function (value) {
		state.shadowHex = value;
		updateShadowVars(null);
	});

	bind('ogc_shadow_opacity', function (value) {
		state.shadowOpacity = value;
		updateShadowVars(null);
	});

	/* -------------------------------------------------------- Buttons/Motion */
	bind('ogc_button_radius', function (value) {
		var px = clamp(parseInt(value, 10) || 999, 0, 999) + 'px';
		setVar('--ogc-btn-radius', px);
		setVar('--ogc-button-radius', px);
	});

	bind('ogc_button_shadow_style', function (value) {
		replaceBodyClass('ogc-btn-shadow-', value);
		updateShadowVars(value);
	});

	bind('ogc_button_hover_effect_primary', function (value) {
		replaceBodyClass('ogc-btn-primary-', value);
	});

	bind('ogc_button_hover_effect_outline', function (value) {
		replaceBodyClass('ogc-btn-outline-', value);
	});

	bind('ogc_button_hover_duration_primary', function (value) {
		var ms = speedMs(value, false) + 'ms';
		setVar('--ogc-hover-duration-primary', ms);
		setVar('--ogc-btn-duration-primary', ms);
	});

	bind('ogc_button_hover_duration_outline', function (value) {
		var ms = speedMs(value, false) + 'ms';
		setVar('--ogc-hover-duration-outline', ms);
		setVar('--ogc-btn-duration-outline', ms);
	});

	bind('ogc_button_hover_intensity_primary', function (value) {
		replaceBodyClass('ogc-btn-primary-intensity-', value);
		var v = intensityValues(value);
		setVar('--ogc-hover-lift-primary', v.lift);
		setVar('--ogc-hover-scale-primary', v.scale);
		setVar('--ogc-hover-glow-primary', v.glow);
	});

	bind('ogc_button_hover_intensity_outline', function (value) {
		replaceBodyClass('ogc-btn-outline-intensity-', value);
		var v = intensityValues(value);
		setVar('--ogc-hover-lift-outline', v.lift);
		setVar('--ogc-hover-scale-outline', v.scale);
		setVar('--ogc-hover-glow-outline', v.glow);
	});

	bind('ogc_button_active_state', function (value) {
		replaceBodyClass('ogc-btn-active-', value);
	});

	bind('ogc_button_disabled_style', function (value) {
		replaceBodyClass('ogc-btn-disabled-', value);
	});

	bind('ogc_button_focus_style', function (value) {
		replaceBodyClass('ogc-btn-focus-', value);
	});

	bind('ogc_transition_speed', function (value) {
		replaceBodyClass('ogc-transition-', value);
		var ms = speedMs(value, true) + 'ms';
		setVar('--ogc-transition-duration', ms);
		setVar('--ogc-transition-speed', ms);
		setVar('--ogc-transition', ms + ' ease');
	});

	bind('ogc_toggle_orbit_animation', function (value) {
		var on = !!value && value !== 'false' && value !== '0';
		body.classList.toggle('ogc-motion-reduced-by-admin', !on);
	});

	bind('ogc_underline_swoosh_style', function (value) {
		replaceBodyClass('ogc-swoosh-', value);
		setVar('--ogc-swoosh-mode', value || 'solid');
	});

	/* ----------------------------------------------------------------- Fonts */
	bind('ogc_font_base_size', function (value) {
		setVar('--ogc-font-size-base', clamp(parseInt(value, 10) || 16, 12, 24) + 'px');
	});
})(jQuery);
