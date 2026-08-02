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
		$root.find('input[type="range"][data-range-output]').each(function () {
			var $input = $(this);
			var unit = '';
			var $out = $('#' + $input.attr('data-range-output'));
			var match = $out.text().match(/[^\d.-]+$/);
			if (match) {
				unit = match[0];
			}
			$input.on('input change', function () {
				$out.text($input.val() + unit);
			});
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

	function bindImage($root) {
		var frame;
		$root.on('click', '[data-image-select]', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-image-field]');
			var $input = $field.find('input[type="hidden"]');
			var $preview = $field.find('.ogc-page-settings__image-preview');
			var $remove = $field.find('[data-image-remove]');

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
				var attachment = frame.state().get('selection').first().toJSON();
				var url = attachment.url || '';
				$input.val(url);
				if (url) {
					$preview.find('img').attr('src', url);
					$preview.prop('hidden', false);
					$remove.prop('hidden', false);
				}
			});

			frame.open();
		});

		$root.on('click', '[data-image-remove]', function (e) {
			e.preventDefault();
			var $field = $(this).closest('[data-image-field]');
			$field.find('input[type="hidden"]').val('');
			$field.find('.ogc-page-settings__image-preview').prop('hidden', true).find('img').attr('src', '');
			$(this).prop('hidden', true);
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
