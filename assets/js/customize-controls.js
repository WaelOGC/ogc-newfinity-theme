/**
 * Customizer controls UI — accordion groups, (?) tooltips, footer columns builder.
 *
 * Groups mirror the OGC NewFinity dashboard section headings. Tooltips reuse
 * schema tooltip strings (localized from PHP), shown on click/hover only.
 * Footer link columns use label + URL inputs over the same JSON theme_mod.
 */
(function ($, api) {
	'use strict';

	var cfg = window.ogcCustomizeControls || {};
	var groups = cfg.groups || {};
	var tooltips = cfg.tooltips || {};
	var i18n = cfg.i18n || {};

	function closeAllTips(except) {
		$('.ogc-cz-tip.is-open').each(function () {
			if (except && this === except) {
				return;
			}
			$(this).removeClass('is-open').attr('aria-expanded', 'false');
			$(this).find('.ogc-cz-tip__bubble').attr('hidden', true);
		});
	}

	function injectTooltip($control, text) {
		if (!text || $control.find('.ogc-cz-tip').length) {
			return;
		}

		var $btn = $(
			'<button type="button" class="ogc-cz-tip" aria-expanded="false" aria-label="' +
				(i18n.moreInfo || 'More info') +
				'">?</button>'
		);
		var $bubble = $('<span class="ogc-cz-tip__bubble" role="tooltip" hidden></span>').text(text);
		$btn.append($bubble);

		var $title = $control.find('.customize-control-title').first();
		if ($title.length) {
			$title.append(' ').append($btn);
		} else {
			var $label = $control.find('> label').first();
			if (!$label.length) {
				return;
			}
			$label.append(' ').append($btn);
		}

		$btn.on('click', function (e) {
			e.preventDefault();
			e.stopPropagation();
			var open = !$btn.hasClass('is-open');
			closeAllTips(open ? $btn[0] : null);
			$btn.toggleClass('is-open', open).attr('aria-expanded', open ? 'true' : 'false');
			$bubble.attr('hidden', !open);
		});

		$btn.on('mouseenter focus', function () {
			closeAllTips($btn[0]);
			$btn.addClass('is-open').attr('aria-expanded', 'true');
			$bubble.attr('hidden', false);
		});

		$btn.on('mouseleave blur', function () {
			window.setTimeout(function () {
				if (!$btn.is(':hover') && !$btn.is(':focus')) {
					$btn.removeClass('is-open').attr('aria-expanded', 'false');
					$bubble.attr('hidden', true);
				}
			}, 120);
		});
	}

	function setGroupOpen($section, groupId, open) {
		$section.find('.ogc-cz-group[data-ogc-group="' + groupId + '"]').each(function () {
			$(this).toggleClass('is-open', open).attr('aria-expanded', open ? 'true' : 'false');
		});
		$section.find('.customize-control.ogc-cz-grouped[data-ogc-group="' + groupId + '"]').each(function () {
			$(this).toggleClass('is-ogc-collapsed', !open);
		});
	}

	function bindGroupToggles($section) {
		$section.find('.ogc-cz-group').off('click.ogcCz').on('click.ogcCz', function (e) {
			e.preventDefault();
			var groupId = $(this).data('ogc-group');
			var open = !$(this).hasClass('is-open');
			setGroupOpen($section, groupId, open);
		});
	}

	/* ------------------------------------------------------ Footer columns */

	function parseColumns(raw) {
		if (Array.isArray(raw)) {
			return raw;
		}
		if (typeof raw !== 'string' || !raw) {
			return [];
		}
		try {
			var parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed : [];
		} catch (e) {
			return [];
		}
	}

	function normalizeColumns(columns) {
		return (columns || []).slice(0, 6).map(function (column) {
			var links = Array.isArray(column.links) ? column.links : [];
			return {
				title: column.title || '',
				links: links.slice(0, 20).map(function (link) {
					return {
						label: (link && link.label) || '',
						url: (link && link.url) || ''
					};
				})
			};
		});
	}

	function initFooterColumns(control) {
		if (!control || !control.container || control.container.data('ogcFooterColsReady')) {
			return;
		}

		var $root = control.container.find('[data-ogc-footer-cols]');
		if (!$root.length) {
			return;
		}

		control.container.data('ogcFooterColsReady', true);

		var state = normalizeColumns(
			control.params.columns || parseColumns(control.setting ? control.setting.get() : '')
		);

		function commit() {
			var json = JSON.stringify(state);
			if (control.setting) {
				control.setting.set(json);
			}
			control.container.find('.ogc-cz-footer-cols__store').val(json);
		}

		function render() {
			$root.empty();

			state.forEach(function (column, columnIndex) {
				var $card = $('<div class="ogc-cz-footer-cols__card"></div>');

				var $head = $('<div class="ogc-cz-footer-cols__head"></div>');
				var $title = $('<input type="text" class="ogc-cz-footer-cols__title" />')
					.attr('placeholder', i18n.columnTitle || 'Column title')
					.val(column.title);
				$title.on('input', function () {
					state[columnIndex].title = $title.val();
					commit();
				});
				$head.append($title);

				var $removeCol = $('<button type="button" class="ogc-cz-footer-cols__remove" aria-label="' +
					(i18n.removeColumn || 'Remove column') +
					'">×</button>');
				$removeCol.on('click', function () {
					state.splice(columnIndex, 1);
					render();
					commit();
				});
				$head.append($removeCol);
				$card.append($head);

				(column.links || []).forEach(function (link, linkIndex) {
					var $row = $('<div class="ogc-cz-footer-cols__row"></div>');

					var $label = $('<input type="text" class="ogc-cz-footer-cols__label" />')
						.attr('placeholder', i18n.linkLabel || 'Label')
						.attr('aria-label', i18n.linkLabel || 'Label')
						.val(link.label);
					$label.on('input', function () {
						state[columnIndex].links[linkIndex].label = $label.val();
						commit();
					});

					// Prefer text+inputmode: defaults use site-relative paths (/services/),
					// which HTML5 type=url marks invalid.
					var $url = $('<input type="text" class="ogc-cz-footer-cols__url" inputmode="url" autocomplete="url" spellcheck="false" />')
						.attr('placeholder', i18n.linkUrl || 'URL')
						.attr('aria-label', i18n.linkUrl || 'URL')
						.val(link.url);
					$url.on('input', function () {
						state[columnIndex].links[linkIndex].url = $url.val();
						commit();
					});

					var $removeLink = $('<button type="button" class="ogc-cz-footer-cols__remove" aria-label="' +
						(i18n.remove || 'Remove') +
						'">×</button>');
					$removeLink.on('click', function () {
						state[columnIndex].links.splice(linkIndex, 1);
						render();
						commit();
					});

					$row.append($label, $url, $removeLink);
					$card.append($row);
				});

				var $addLink = $('<button type="button" class="button-link ogc-cz-footer-cols__add-link"></button>')
					.text(i18n.addLink || 'Add link');
				$addLink.on('click', function () {
					if (!Array.isArray(state[columnIndex].links)) {
						state[columnIndex].links = [];
					}
					state[columnIndex].links.push({ label: '', url: '' });
					render();
					commit();
				});
				$card.append($addLink);

				$root.append($card);
			});

			var $addCol = $('<button type="button" class="button ogc-cz-footer-cols__add-col"></button>')
				.text(i18n.addColumn || 'Add column');
			$addCol.on('click', function () {
				state.push({ title: '', links: [] });
				render();
				commit();
			});
			$root.append($addCol);
		}

		render();
	}

	function initSection(section) {
		if (!section || !section.container) {
			return;
		}
		var $section = $(section.contentContainer || section.container);
		if (!$section.length || $section.data('ogcCzReady')) {
			return;
		}
		$section.data('ogcCzReady', true);

		api.control.each(function (control) {
			if (!control || !control.params || control.params.section !== section.id) {
				return;
			}
			var id = control.id;
			var $el = control.container;
			if (!$el || !$el.length) {
				return;
			}

			if (groups[id]) {
				$el.addClass('ogc-cz-grouped').attr('data-ogc-group', groups[id]);
			}
			if (tooltips[id]) {
				injectTooltip($el, tooltips[id]);
			}
			if (control.params.type === 'ogc_footer_columns') {
				initFooterColumns(control);
			}
		});

		$section.find('.ogc-cz-group').each(function () {
			var groupId = $(this).data('ogc-group');
			var open = $(this).hasClass('is-open') || $(this).attr('aria-expanded') === 'true';
			setGroupOpen($section, groupId, open);
		});

		bindGroupToggles($section);
	}

	$(document).on('click.ogcCzTip', function (e) {
		if (!$(e.target).closest('.ogc-cz-tip').length) {
			closeAllTips();
		}
	});

	api.bind('ready', function () {
		api.section.each(function (section) {
			section.expanded.bind(function (isExpanded) {
				if (isExpanded) {
					window.setTimeout(function () {
						initSection(section);
					}, 0);
				}
			});
			if (section.expanded.get()) {
				initSection(section);
			}
		});
	});
})(jQuery, wp.customize);
