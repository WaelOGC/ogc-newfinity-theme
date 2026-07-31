/**
 * OGC NewFinity — Theme Options admin panel behaviour.
 *
 * Tabs, colour syncing, media pickers, colour presets, and the JSON-backed
 * builders for navigation order, footer columns, and ad slots.
 */
(function () {
	'use strict';

	var data = window.ogcThemeOptions || {};
	var strings = data.strings || {};
	var root = document.querySelector('.ogc-opt');

	if (!root) {
		return;
	}

	/* ------------------------------------------------------------- Helpers */

	function el(tag, className, text) {
		var node = document.createElement(tag);
		if (className) {
			node.className = className;
		}
		if (text !== undefined) {
			node.textContent = text;
		}
		return node;
	}

	function input(type, value, placeholder) {
		var node = document.createElement('input');
		node.type = type;
		node.value = value || '';
		if (placeholder) {
			node.placeholder = placeholder;
		}
		return node;
	}

	function parseJson(value, fallback) {
		try {
			var parsed = JSON.parse(value);
			return Array.isArray(parsed) ? parsed : fallback;
		} catch (e) {
			return fallback;
		}
	}

	function markDirty() {
		var hint = document.getElementById('ogc-opt-dirty');
		if (hint) {
			hint.hidden = false;
		}
		root.dataset.dirty = '1';
	}

	/* ---------------------------------------------------------------- Tabs */

	var tabs = Array.prototype.slice.call(root.querySelectorAll('.ogc-opt__tab'));
	var panels = Array.prototype.slice.call(root.querySelectorAll('.ogc-opt__panel'));
	var activeTabInput = document.getElementById('ogc-active-tab');

	function activateTab(id, updateHash) {
		var found = false;

		tabs.forEach(function (tab) {
			var isActive = tab.dataset.tab === id;
			tab.classList.toggle('is-active', isActive);
			tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
			found = found || isActive;
		});

		if (!found) {
			return;
		}

		panels.forEach(function (panel) {
			var isActive = panel.dataset.panel === id;
			panel.classList.toggle('is-active', isActive);
			panel.hidden = !isActive;
		});

		if (activeTabInput) {
			activeTabInput.value = id;
		}

		if (updateHash) {
			history.replaceState(null, '', '#' + id);
		}
	}

	tabs.forEach(function (tab) {
		tab.addEventListener('click', function () {
			activateTab(tab.dataset.tab, true);
		});
	});

	if (window.location.hash) {
		activateTab(window.location.hash.replace('#', ''), false);
	}

	/* -------------------------------------------------------------- Colors */

	root.addEventListener('input', function (event) {
		var target = event.target;

		if (target.classList.contains('ogc-opt__color-swatch')) {
			var text = document.getElementById(target.dataset.sync);
			if (text) {
				text.value = target.value.toUpperCase();
			}
		}

		if (target.classList.contains('ogc-opt__color-text')) {
			var swatch = root.querySelector('.ogc-opt__color-swatch[data-sync="' + target.id + '"]');
			if (swatch && /^#[0-9a-f]{6}$/i.test(target.value.trim())) {
				swatch.value = target.value.trim();
			}
		}

		if (target.type === 'range' && target.dataset.rangeOutput) {
			var output = document.getElementById(target.dataset.rangeOutput);
			if (output) {
				var unit = output.textContent.replace(/^[-\d.]+/, '');
				output.textContent = target.value + unit;
			}
		}

		markDirty();
	});

	root.addEventListener('change', markDirty);

	root.addEventListener('click', function (event) {
		var reset = event.target.closest('.ogc-opt__color-reset');
		if (!reset) {
			return;
		}

		event.preventDefault();
		var row = reset.closest('.ogc-opt__color');
		var text = row.querySelector('.ogc-opt__color-text');
		var swatch = row.querySelector('.ogc-opt__color-swatch');
		text.value = reset.dataset.default;
		swatch.value = reset.dataset.default;
		markDirty();
	});

	/* ------------------------------------------------------------- Presets */

	function applyPreset(name) {
		var preset = (data.presets || {})[name];
		if (!preset) {
			return;
		}

		Object.keys(preset).forEach(function (key) {
			var text = document.getElementById(key);
			var swatch = root.querySelector('.ogc-opt__color-swatch[data-sync="' + key + '"]');
			if (text) {
				text.value = preset[key];
			}
			if (swatch) {
				swatch.value = preset[key];
			}
		});

		markDirty();
	}

	root.addEventListener('click', function (event) {
		var button = event.target.closest('.ogc-opt__preset');
		if (button) {
			event.preventDefault();
			applyPreset(button.dataset.preset);
		}
	});

	/* --------------------------------------------------------- Media picker */

	var mediaFrames = {};

	function openMedia(hiddenInput, wrapper) {
		if (!window.wp || !window.wp.media) {
			return;
		}

		var id = hiddenInput.id;

		if (!mediaFrames[id]) {
			mediaFrames[id] = window.wp.media({
				title: strings.chooseImage || 'Choose image',
				button: { text: strings.useImage || 'Use this image' },
				library: { type: 'image' },
				multiple: false
			});

			mediaFrames[id].on('select', function () {
				var attachment = mediaFrames[id].state().get('selection').first().toJSON();
				hiddenInput.value = attachment.url;
				renderImagePreview(wrapper, attachment.url);
				markDirty();
			});
		}

		mediaFrames[id].open();
	}

	function renderImagePreview(wrapper, url) {
		var preview = wrapper.querySelector('.ogc-opt__image-preview');
		var remove = wrapper.querySelector('[data-image-remove]');

		if (preview) {
			preview.hidden = !url;
			var img = preview.querySelector('img');
			if (img) {
				img.src = url || '';
			}
		}

		if (remove) {
			remove.hidden = !url;
		}
	}

	root.addEventListener('click', function (event) {
		var select = event.target.closest('[data-image-select]');
		if (select) {
			event.preventDefault();
			var wrapper = select.closest('[data-image-field]');
			openMedia(wrapper.querySelector('input[type="hidden"]'), wrapper);
			return;
		}

		var remove = event.target.closest('[data-image-remove]');
		if (remove) {
			event.preventDefault();
			var box = remove.closest('[data-image-field]');
			box.querySelector('input[type="hidden"]').value = '';
			renderImagePreview(box, '');
			markDirty();
		}
	});

	/* ------------------------------------------------------------ Builders */

	var builders = Array.prototype.slice.call(root.querySelectorAll('.ogc-opt__builder'));

	builders.forEach(function (container) {
		var store = document.getElementById(container.dataset.input);
		if (!store) {
			return;
		}

		var type = container.dataset.builder;
		var state = parseJson(store.value, []);

		function commit() {
			store.value = JSON.stringify(state);
			markDirty();
		}

		function rerender() {
			commit();
			render();
		}

		function render() {
			container.innerHTML = '';

			if (type === 'nav_order') {
				renderNavOrder();
			} else if (type === 'footer_columns') {
				renderFooterColumns();
			} else if (type === 'ads_config') {
				renderAdsConfig();
			}
		}

		/* ---------------------------------------------------- Nav ordering */

		function renderNavOrder() {
			var list = el('div', 'ogc-opt__rows');
			var labels = data.navLabels || {};

			state.forEach(function (item, index) {
				var row = el('div', 'ogc-opt__sortrow');
				row.draggable = true;
				row.dataset.index = String(index);

				var grip = el('span', 'ogc-opt__grip', '⋮⋮');
				grip.title = strings.dragHandle || 'Drag to reorder';
				row.appendChild(grip);

				row.appendChild(el('span', 'ogc-opt__sortrow-label', labels[item.slug] || item.slug));

				var toggle = el('label', 'ogc-opt__switch');
				var checkbox = input('checkbox');
				checkbox.checked = item.visible !== false;
				checkbox.addEventListener('change', function () {
					state[index].visible = checkbox.checked;
					commit();
				});
				toggle.appendChild(checkbox);
				toggle.appendChild(el('span', 'ogc-opt__switch-track'));
				toggle.appendChild(el('span', 'ogc-opt__switch-text', strings.visible || 'Visible'));
				row.appendChild(toggle);

				attachDrag(row, list);
				list.appendChild(row);
			});

			container.appendChild(list);
		}

		function attachDrag(row, list) {
			row.addEventListener('dragstart', function (event) {
				row.classList.add('is-dragging');
				event.dataTransfer.effectAllowed = 'move';
				event.dataTransfer.setData('text/plain', row.dataset.index);
			});

			row.addEventListener('dragend', function () {
				row.classList.remove('is-dragging');
				Array.prototype.forEach.call(list.children, function (child) {
					child.classList.remove('is-over');
				});
			});

			row.addEventListener('dragover', function (event) {
				event.preventDefault();
				event.dataTransfer.dropEffect = 'move';
				row.classList.add('is-over');
			});

			row.addEventListener('dragleave', function () {
				row.classList.remove('is-over');
			});

			row.addEventListener('drop', function (event) {
				event.preventDefault();
				var from = parseInt(event.dataTransfer.getData('text/plain'), 10);
				var to = parseInt(row.dataset.index, 10);

				if (isNaN(from) || isNaN(to) || from === to) {
					return;
				}

				var moved = state.splice(from, 1)[0];
				state.splice(to, 0, moved);
				rerender();
			});
		}

		/* -------------------------------------------------- Footer columns */

		function renderFooterColumns() {
			state.forEach(function (column, columnIndex) {
				var card = el('div', 'ogc-opt__card');

				var head = el('div', 'ogc-opt__card-head');
				var title = input('text', column.title, strings.columnTitle || 'Column title');
				title.addEventListener('input', function () {
					state[columnIndex].title = title.value;
					commit();
				});
				head.appendChild(title);

				var removeColumn = el('button', 'ogc-opt__row-remove', '×');
				removeColumn.type = 'button';
				removeColumn.title = strings.removeColumn || 'Remove column';
				removeColumn.addEventListener('click', function () {
					state.splice(columnIndex, 1);
					rerender();
				});
				head.appendChild(removeColumn);
				card.appendChild(head);

				(column.links || []).forEach(function (link, linkIndex) {
					var row = el('div', 'ogc-opt__linkrow');

					var label = input('text', link.label, strings.linkLabel || 'Label');
					label.addEventListener('input', function () {
						state[columnIndex].links[linkIndex].label = label.value;
						commit();
					});

					var url = input('text', link.url, strings.linkUrl || 'URL');
					url.addEventListener('input', function () {
						state[columnIndex].links[linkIndex].url = url.value;
						commit();
					});

					var removeLink = el('button', 'ogc-opt__row-remove', '×');
					removeLink.type = 'button';
					removeLink.title = strings.remove || 'Remove';
					removeLink.addEventListener('click', function () {
						state[columnIndex].links.splice(linkIndex, 1);
						rerender();
					});

					row.appendChild(label);
					row.appendChild(url);
					row.appendChild(removeLink);
					card.appendChild(row);
				});

				var addLink = el('button', 'ogc-opt__btn ogc-opt__btn--ghost', strings.addLink || 'Add link');
				addLink.type = 'button';
				addLink.addEventListener('click', function () {
					if (!Array.isArray(state[columnIndex].links)) {
						state[columnIndex].links = [];
					}
					state[columnIndex].links.push({ label: '', url: '' });
					rerender();
				});
				card.appendChild(addLink);

				container.appendChild(card);
			});

			var addColumn = el('button', 'ogc-opt__btn', strings.addColumn || 'Add column');
			addColumn.type = 'button';
			addColumn.addEventListener('click', function () {
				state.push({ title: '', links: [] });
				rerender();
			});
			container.appendChild(addColumn);
		}

		/* ------------------------------------------------------- Ad slots */

		function renderAdsConfig() {
			state.forEach(function (slot, index) {
				var card = el('div', 'ogc-opt__card');

				var head = el('div', 'ogc-opt__card-head');
				var key = input('text', slot.key, strings.slotKey || 'Slot key');
				key.addEventListener('input', function () {
					state[index].key = key.value;
					commit();
				});
				head.appendChild(key);

				var enabled = el('label', 'ogc-opt__switch');
				var checkbox = input('checkbox');
				checkbox.checked = !!slot.enabled;
				checkbox.addEventListener('change', function () {
					state[index].enabled = checkbox.checked;
					commit();
				});
				enabled.appendChild(checkbox);
				enabled.appendChild(el('span', 'ogc-opt__switch-track'));
				enabled.appendChild(el('span', 'ogc-opt__switch-text', strings.slotEnabled || 'Enabled'));
				head.appendChild(enabled);

				var removeSlot = el('button', 'ogc-opt__row-remove', '×');
				removeSlot.type = 'button';
				removeSlot.title = strings.remove || 'Remove';
				removeSlot.addEventListener('click', function () {
					state.splice(index, 1);
					rerender();
				});
				head.appendChild(removeSlot);
				card.appendChild(head);

				var typeRow = el('div', 'ogc-opt__field-inline');
				typeRow.appendChild(el('span', null, strings.slotType || 'Type'));
				var select = document.createElement('select');
				[['image', strings.slotImage || 'Image'], ['code', strings.slotCode || 'Embed code']].forEach(function (pair) {
					var option = document.createElement('option');
					option.value = pair[0];
					option.textContent = pair[1];
					option.selected = slot.type === pair[0];
					select.appendChild(option);
				});
				select.addEventListener('change', function () {
					state[index].type = select.value;
					rerender();
				});
				typeRow.appendChild(select);
				card.appendChild(typeRow);

				if (slot.type === 'code') {
					var codeRow = el('div', 'ogc-opt__field-inline');
					codeRow.appendChild(el('span', null, strings.slotCode || 'Embed code'));
					var code = document.createElement('textarea');
					code.rows = 5;
					code.value = slot.code || '';
					code.addEventListener('input', function () {
						state[index].code = code.value;
						commit();
					});
					codeRow.appendChild(code);
					card.appendChild(codeRow);
				} else {
					var imageRow = el('div', 'ogc-opt__field-inline');
					imageRow.appendChild(el('span', null, strings.slotImage || 'Image'));
					var image = input('text', slot.image, 'https://');
					image.addEventListener('input', function () {
						state[index].image = image.value;
						commit();
					});
					imageRow.appendChild(image);
					card.appendChild(imageRow);

					var urlRow = el('div', 'ogc-opt__field-inline');
					urlRow.appendChild(el('span', null, strings.slotLinkUrl || 'Click-through URL'));
					var url = input('text', slot.url, 'https://');
					url.addEventListener('input', function () {
						state[index].url = url.value;
						commit();
					});
					urlRow.appendChild(url);
					card.appendChild(urlRow);
				}

				container.appendChild(card);
			});

			var addSlot = el('button', 'ogc-opt__btn', strings.addSlot || 'Add ad slot');
			addSlot.type = 'button';
			addSlot.addEventListener('click', function () {
				state.push({ key: '', enabled: false, type: 'image', image: '', url: '', code: '' });
				rerender();
			});
			container.appendChild(addSlot);
		}

		render();
	});

	/* ----------------------------------------------- Destructive confirms */

	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-confirm]');
		if (!button) {
			return;
		}

		var message = button.dataset.confirm === 'all'
			? (strings.confirmResetAll || 'Reset every theme option?')
			: (strings.confirmResetTab || 'Reset this tab?');

		if (!window.confirm(message)) {
			event.preventDefault();
		}
	});

	/* --------------------------------------------------- Unsaved warning */

	Array.prototype.forEach.call(root.querySelectorAll('form'), function (form) {
		form.addEventListener('submit', function () {
			delete root.dataset.dirty;
		});
	});

	window.addEventListener('beforeunload', function (event) {
		if (root.dataset.dirty === '1') {
			event.preventDefault();
			event.returnValue = '';
		}
	});
})();
