/**
 * OGC NewFinity — Pricing page service-line tabs + hash deep links.
 * Enqueued only on the Pricing template.
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-ogc-pricing]');
	var tabsRoot = root ? root.querySelector('[data-ogc-pricing-tabs]') : null;

	if (!root || !tabsRoot) {
		return;
	}

	var tabs = tabsRoot.querySelectorAll('[data-ogc-pricing-tab]');
	var panels = tabsRoot.querySelectorAll('[data-ogc-pricing-panel]');

	if (!tabs.length || !panels.length) {
		return;
	}

	var slugToTab = Object.create(null);
	var slugToPanel = Object.create(null);

	for (var i = 0; i < tabs.length; i++) {
		var tabSlug = tabs[i].getAttribute('data-ogc-pricing-tab');
		if (tabSlug) {
			slugToTab[tabSlug] = tabs[i];
		}
	}

	for (var p = 0; p < panels.length; p++) {
		var panelSlug = panels[p].getAttribute('data-ogc-pricing-panel');
		if (panelSlug) {
			slugToPanel[panelSlug] = panels[p];
		}
	}

	function getSlugFromHash() {
		var hash = window.location.hash.replace(/^#/, '');
		return hash && slugToPanel[hash] ? hash : '';
	}

	function setTabFocusOrder(activeTab) {
		for (var t = 0; t < tabs.length; t++) {
			tabs[t].tabIndex = tabs[t] === activeTab ? 0 : -1;
		}
	}

	function activate(slug, updateHash) {
		if (!slug || !slugToPanel[slug]) {
			slug = tabs[0].getAttribute('data-ogc-pricing-tab');
		}

		var activeTab = slugToTab[slug];
		var activePanel = slugToPanel[slug];

		if (!activeTab || !activePanel) {
			return;
		}

		for (var i = 0; i < tabs.length; i++) {
			var isActive = tabs[i] === activeTab;
			tabs[i].setAttribute('aria-selected', isActive ? 'true' : 'false');
		}

		for (var j = 0; j < panels.length; j++) {
			var show = panels[j] === activePanel;
			panels[j].hidden = !show;
		}

		setTabFocusOrder(activeTab);

		if (updateHash && window.location.hash.replace(/^#/, '') !== slug) {
			if (window.history && window.history.replaceState) {
				window.history.replaceState(null, '', '#' + slug);
			} else {
				window.location.hash = slug;
			}
		}
	}

	function onTabClick(event) {
		var tab = event.currentTarget;
		var slug = tab.getAttribute('data-ogc-pricing-tab');
		if (slug) {
			activate(slug, true);
			tab.focus();
		}
	}

	function onTabKeydown(event) {
		var key = event.key;
		if (key !== 'ArrowLeft' && key !== 'ArrowRight' && key !== 'Home' && key !== 'End') {
			return;
		}

		var current = event.currentTarget;
		var list = Array.prototype.slice.call(tabs);
		var index = list.indexOf(current);

		if (index < 0) {
			return;
		}

		var next = index;

		if (key === 'ArrowLeft') {
			next = index > 0 ? index - 1 : list.length - 1;
		} else if (key === 'ArrowRight') {
			next = index < list.length - 1 ? index + 1 : 0;
		} else if (key === 'Home') {
			next = 0;
		} else if (key === 'End') {
			next = list.length - 1;
		}

		event.preventDefault();
		var nextTab = list[next];
		var slug = nextTab.getAttribute('data-ogc-pricing-tab');
		if (slug) {
			activate(slug, true);
			nextTab.focus();
		}
	}

	root.classList.add('ogc-pricing--tabs');

	for (var k = 0; k < tabs.length; k++) {
		tabs[k].addEventListener('click', onTabClick);
		tabs[k].addEventListener('keydown', onTabKeydown);
	}

	window.addEventListener('hashchange', function () {
		var slug = getSlugFromHash();
		if (slug) {
			activate(slug, false);
		}
	});

	activate(getSlugFromHash() || tabs[0].getAttribute('data-ogc-pricing-tab'), false);
})();
