/**
 * OGC NewFinity — Help & Support FAQ search + accordion.
 * Enqueued only on /support/ (page slug: support).
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-ogc-faq-filter]');
	if (!root) {
		return;
	}

	var input = root.querySelector('#ogc-faq-search');
	var empty = root.querySelector('[data-ogc-faq-empty]');
	var items = root.querySelectorAll('[data-ogc-faq-item]');
	var groups = root.querySelectorAll('[data-ogc-faq-group]');

	function normalize(value) {
		return String(value || '')
			.toLowerCase()
			.replace(/\s+/g, ' ')
			.trim();
	}

	function filterFaqs() {
		var query = normalize(input ? input.value : '');
		var visibleCount = 0;

		for (var i = 0; i < items.length; i++) {
			var item = items[i];
			var question = normalize(item.getAttribute('data-question'));
			var match = !query || question.indexOf(query) !== -1;
			item.hidden = !match;
			if (match) {
				visibleCount += 1;
			}
		}

		for (var g = 0; g < groups.length; g++) {
			var group = groups[g];
			var groupItems = group.querySelectorAll('[data-ogc-faq-item]');
			var groupVisible = 0;
			for (var j = 0; j < groupItems.length; j++) {
				if (!groupItems[j].hidden) {
					groupVisible += 1;
				}
			}
			group.hidden = groupVisible === 0;
		}

		if (empty) {
			empty.hidden = visibleCount > 0;
		}
	}

	function closeItem(item) {
		var trigger = item.querySelector('.ogc-faq-item__trigger');
		var panel = item.querySelector('.ogc-faq-item__panel');
		if (!trigger || !panel) {
			return;
		}
		trigger.setAttribute('aria-expanded', 'false');
		panel.hidden = true;
		item.classList.remove('is-open');
	}

	function openItem(item) {
		var trigger = item.querySelector('.ogc-faq-item__trigger');
		var panel = item.querySelector('.ogc-faq-item__panel');
		if (!trigger || !panel) {
			return;
		}
		trigger.setAttribute('aria-expanded', 'true');
		panel.hidden = false;
		item.classList.add('is-open');
	}

	function toggleItem(item) {
		var trigger = item.querySelector('.ogc-faq-item__trigger');
		if (!trigger) {
			return;
		}
		var isOpen = trigger.getAttribute('aria-expanded') === 'true';
		if (isOpen) {
			closeItem(item);
			return;
		}
		openItem(item);
	}

	root.addEventListener('click', function (event) {
		var trigger = event.target.closest('.ogc-faq-item__trigger');
		if (!trigger || !root.contains(trigger)) {
			return;
		}
		var item = trigger.closest('[data-ogc-faq-item]');
		if (!item) {
			return;
		}
		toggleItem(item);
	});

	if (input) {
		input.addEventListener('input', filterFaqs);
		input.addEventListener('search', filterFaqs);
	}
})();
