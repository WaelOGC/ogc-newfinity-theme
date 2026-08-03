/**
 * OGC Page Settings meta box — image picker, ranges, border panel, accordions.
 */
(function ($) {
	'use strict';

	function syncFitVisibility($root) {
		var fit = $root.find('#_ogc_hero_bg_fit').val();
		$root.find('[data-show-when-fit]').each(function () {
			var need = $(this).attr('data-show-when-fit');
			$(this).prop('hidden', fit !== need);
		});
	}

	function syncWidgetHide($root) {
		var enabled = $root.find('#_ogc_page_enable_widget_area').is(':checked');
		var $hide = $root.find('#_ogc_hide_widget_area');
		$hide.prop('disabled', !enabled);
		if (!enabled) {
			$hide.prop('checked', false);
		}
		$root.find('#ogc-ps-hide-widget-row').toggleClass('is-disabled', !enabled);
	}

	function bindRanges($root) {
		$root.find('input[type="range"]').each(function () {
			var input = this;
			var $input = $(input);
			var unit = '';
			var outId = $input.attr('data-range-output');
			var $out = outId ? $('#' + outId) : $();

			if ($out.length) {
				var match = $out.text().match(/[^\d.-]+$/);
				if (match) {
					unit = match[0];
				}
			}

			function updateRange() {
				var min = parseFloat(input.min);
				var max = parseFloat(input.max);
				var val = parseFloat(input.value);

				if (isNaN(min)) {
					min = 0;
				}
				if (isNaN(max) || max === min) {
					max = min + 100;
				}
				if (isNaN(val)) {
					val = min;
				}

				var pct = Math.max(0, Math.min(100, ((val - min) / (max - min)) * 100));
				input.style.setProperty('--ogc-ps-range-fill', pct + '%');

				if ($out.length) {
					$out.text(input.value + unit);
				}
			}

			$input.on('input change', updateRange);
			updateRange();
		});
	}

	function bindColors($root) {
		$root.on('input change', '.ogc-page-settings__color-swatch', function () {
			var id = $(this).attr('data-sync');
			var $text = $root.find('#' + id);
			if ($text.length) {
				$text.val($(this).val());
			}
		});
		$root.on('change blur', '.ogc-page-settings__color-text', function () {
			var val = $(this).val().trim();
			var $swatch = $root.find('.ogc-page-settings__color-swatch[data-sync="' + this.id + '"]');
			if (/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/.test(val)) {
				$swatch.val(val);
			}
		});
	}

	function syncImageButtons($field, hasValue) {
		$field.find('[data-image-select]').prop('hidden', !!hasValue);
		$field.find('[data-image-replace]').prop('hidden', !hasValue);
		$field.find('[data-image-remove]').prop('hidden', !hasValue);
		$field.find('.ogc-page-settings__image-preview').prop('hidden', !hasValue);
	}

	function bindImage($root) {
		var frame;
		var $activeField = null;

		function openMedia($field) {
			$activeField = $field;
			if (frame) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: 'Choose image',
				button: { text: 'Use image' },
				multiple: false
			});

			frame.on('select', function () {
				if (!$activeField || !$activeField.length) {
					return;
				}
				var attachment = frame.state().get('selection').first().toJSON();
				var url = attachment.url || '';
				var $input = $activeField.find('input[type="hidden"]');
				var $preview = $activeField.find('.ogc-page-settings__image-preview');
				$input.val(url);
				if (url) {
					$preview.find('img').attr('src', url);
				}
				syncImageButtons($activeField, !!url);
			});

			frame.open();
		}

		$root.on('click', '[data-image-select], [data-image-replace]', function (e) {
			e.preventDefault();
			openMedia($(this).closest('[data-image-field]'));
		});

		$root.on('click', '[data-image-remove]', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-image-field]');
			$field.find('input[type="hidden"]').val('');
			$field.find('.ogc-page-settings__image-preview').find('img').attr('src', '');
			syncImageButtons($field, false);
		});
	}

	function bindAccordions($root) {
		$root.children('.ogc-ps-card').on('toggle', function (e) {
			if (e.target !== this || !this.open) {
				return;
			}
			$root.children('.ogc-ps-card').not(this).each(function () {
				this.open = false;
			});
		});
	}

	function setDimensionsLinked($block, linked) {
		var $inputs = $block.find('.ogc-ps-dimensions__input');
		var $link = $block.find('[data-dimensions-link]');
		var $hidden = $block.find('[data-dimensions-linked]');
		var $top = $inputs.filter('[data-side="top"]');

		$hidden.val(linked ? '1' : '0');
		$link.toggleClass('is-linked', linked).attr('aria-pressed', linked ? 'true' : 'false');

		$inputs.each(function () {
			var side = $(this).attr('data-side');
			if (side === 'top') {
				$(this).prop('readonly', false);
				return;
			}
			$(this).prop('readonly', linked);
			if (linked) {
				$(this).val($top.val());
			}
		});
	}

	function bindBorderPanel($root) {
		var $panel = $root.find('[data-border-panel]');
		if (!$panel.length) {
			return;
		}

		function syncBorderType() {
			var type = $panel.find('[data-border-type]').val();
			var show = type && type !== 'default' && type !== 'none';
			$panel.find('[data-border-dependent]').prop('hidden', !show);
		}

		$panel.on('change', '[data-border-type]', syncBorderType);

		syncBorderType();

		$panel.find('[data-dimensions]').each(function () {
			var $block = $(this);
			setDimensionsLinked($block, $block.find('[data-dimensions-linked]').val() === '1');
		});

		$panel.on('click', '[data-dimensions-link]', function (e) {
			e.preventDefault();
			var $block = $(this).closest('[data-dimensions]');
			var linked = !$(this).hasClass('is-linked');
			setDimensionsLinked($block, linked);
		});

		$panel.on('input change', '.ogc-ps-dimensions__input[data-side="top"]', function () {
			var $block = $(this).closest('[data-dimensions]');
			if ($block.find('[data-dimensions-linked]').val() !== '1') {
				return;
			}
			var val = $(this).val();
			$block.find('.ogc-ps-dimensions__input').not(this).val(val);
		});

		$panel.on('click', '[data-shadow-toggle]', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var $shadow = $panel.find('[data-shadow-panel]');
			var $enabled = $panel.find('[data-shadow-enabled]');
			var opening = $shadow.prop('hidden');

			$shadow.prop('hidden', !opening);
			$btn.toggleClass('is-active', opening).attr('aria-expanded', opening ? 'true' : 'false');
			$enabled.val(opening ? '1' : '0');
		});
	}

	function bindOverlayToggle($root) {
		var $toggle = $root.find('#ogc-ps-overlay-enable');
		if (!$toggle.length) {
			return;
		}

		var $colorRow = $root.find('[data-field="_ogc_hero_bg_overlay_color"]');
		var $opacityRow = $root.find('[data-field="_ogc_hero_bg_overlay_opacity"]');
		var $blendRow = $root.find('[data-field="_ogc_hero_bg_blend_mode"]');
		var $colorText = $root.find('#_ogc_hero_bg_overlay_color');
		var $colorSwatch = $root.find('.ogc-page-settings__color-swatch[data-sync="_ogc_hero_bg_overlay_color"]');
		var defaultColor = $toggle.attr('data-default-color') || '#F7931E';

		function syncOverlayRows() {
			var on = $toggle.is(':checked');
			$colorRow.prop('hidden', !on);
			$opacityRow.prop('hidden', !on);
			$blendRow.prop('hidden', !on);
		}

		$toggle.on('change', function () {
			var on = $toggle.is(':checked');
			if (on) {
				if (!$colorText.val()) {
					$colorText.val(defaultColor);
					$colorSwatch.val(defaultColor);
				}
			} else {
				$colorText.val('');
			}
			syncOverlayRows();
		});

		syncOverlayRows();
	}

	function bindTips($root) {
		function closeTip($tip) {
			$tip.attr('aria-expanded', 'false').removeClass('is-flip-down');
		}

		function closeAll(except) {
			$root.find('.ogc-ps-tip[aria-expanded="true"]').each(function () {
				if (except && this === except) {
					return;
				}
				closeTip($(this));
			});
		}

		function positionTip($tip) {
			$tip.removeClass('is-flip-down');
			var bubble = $tip.find('.ogc-ps-tip__bubble')[0];
			if (!bubble) {
				return;
			}
			var rect = bubble.getBoundingClientRect();
			if (rect.top < 8) {
				$tip.addClass('is-flip-down');
			}
		}

		$root.on('click', '.ogc-ps-tip', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var tip = this;
			var $tip = $(tip);
			var open = $tip.attr('aria-expanded') === 'true';
			closeAll(tip);
			if (open) {
				closeTip($tip);
				return;
			}
			$tip.attr('aria-expanded', 'true');
			positionTip($tip);
		});

		$(document).on('click.ogcPsTip', function (e) {
			if ($(e.target).closest('.ogc-ps-tip').length) {
				return;
			}
			closeAll();
		});

		$(document).on('keydown.ogcPsTip', function (e) {
			if (e.key === 'Escape') {
				closeAll();
			}
		});
	}

	$(function () {
		var $root = $('#ogc-page-settings');
		if (!$root.length) {
			return;
		}

		bindRanges($root);
		bindColors($root);
		bindImage($root);
		bindAccordions($root);
		bindBorderPanel($root);
		bindOverlayToggle($root);
		bindTips($root);
		syncFitVisibility($root);
		syncWidgetHide($root);

		$root.on('change', '#_ogc_hero_bg_fit', function () {
			syncFitVisibility($root);
		});
		$root.on('change', '#_ogc_page_enable_widget_area', function () {
			syncWidgetHide($root);
		});
	});
})(jQuery);
