/**
 * Customizer live preview — postMessage bindings for colors + swoosh.
 *
 * Font selects use transport: refresh (full reload). Loading Google Fonts
 * live via JS in the preview iframe is out of scope; only colors and swoosh
 * get instant :root updates without a page reload.
 */
(function ($) {
	'use strict';

	var root = document.documentElement;

	function setVar(name, value) {
		if (value) {
			root.style.setProperty(name, value);
		}
	}

	function bindColor(settingId, cssVar, aliasVar) {
		wp.customize(settingId, function (setting) {
			setting.bind(function (value) {
				setVar(cssVar, value);
				if (aliasVar) {
					setVar(aliasVar, value);
				}
			});
		});
	}

	bindColor('ogc_color_accent_start', '--accent-gradient-start');
	bindColor('ogc_color_accent_end', '--accent-gradient-end');
	bindColor('ogc_color_bg', '--bg-primary');
	bindColor('ogc_color_text_primary', '--text-primary', '--ogc-text-primary');
	bindColor('ogc_color_text_secondary', '--text-secondary');

	wp.customize('ogc_underline_swoosh_style', function (setting) {
		setting.bind(function (value) {
			var mode = value || 'gradient';
			setVar('--ogc-swoosh-mode', mode);
			document.body.classList.remove('ogc-swoosh-gradient', 'ogc-swoosh-solid', 'ogc-swoosh-none');
			document.body.classList.add('ogc-swoosh-' + mode);
		});
	});
}(jQuery));
