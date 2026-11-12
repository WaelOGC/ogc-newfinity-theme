/**
 * OGC NewFinity — Pricing page service-line navigation + hash deep links.
 * Enqueued only on the Pricing template.
 */
(function () {
	'use strict';

	var root = document.querySelector('[data-ogc-pricing]');
	var tabsRoot = root ? root.querySelector('[data-ogc-pricing-tabs]') : null;

	if (!root || !tabsRoot) {
		return;
	}

	var tablist = tabsRoot.querySelector('[data-ogc-pricing-tablist]');
	var tabs = tabsRoot.querySelectorAll('[data-ogc-pricing-tab]');
	var panels = tabsRoot.querySelectorAll('[data-ogc-pricing-panel]');
	var desktopQuery = window.matchMedia('(min-width: 1024px)');

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

	function setTablistOrientation() {
		if (!tablist) {
			return;
		}
		tablist.setAttribute('aria-orientation', desktopQuery.matches ? 'vertical' : 'vertical');
	}

	function setTabFocusOrder(activeTab) {
		for (var t = 0; t < tabs.length; t++) {
			tabs[t].tabIndex = tabs[t] === activeTab ? 0 : -1;
		}
	}

	function scrollPanelIntoView(panel) {
		if (desktopQuery.matches || !panel) {
			return;
		}
		var header = panel.querySelector('.ogc-pricing-tabpanel__header');
		if (!header) {
			return;
		}
		header.scrollIntoView({ behavior: 'smooth', block: 'start' });
	}

	function activate(slug, updateHash, shouldScroll) {
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

		if (shouldScroll) {
			scrollPanelIntoView(activePanel);
		}
	}

	function onTabClick(event) {
		var tab = event.currentTarget;
		var slug = tab.getAttribute('data-ogc-pricing-tab');
		if (slug) {
			activate(slug, true, true);
			tab.focus();
		}
	}

	function onTabKeydown(event) {
		var key = event.key;
		if (
			key !== 'ArrowUp' &&
			key !== 'ArrowDown' &&
			key !== 'ArrowLeft' &&
			key !== 'ArrowRight' &&
			key !== 'Home' &&
			key !== 'End'
		) {
			return;
		}

		var current = event.currentTarget;
		var list = Array.prototype.slice.call(tabs);
		var index = list.indexOf(current);

		if (index < 0) {
			return;
		}

		var next = index;

		if (key === 'ArrowUp' || key === 'ArrowLeft') {
			next = index > 0 ? index - 1 : list.length - 1;
		} else if (key === 'ArrowDown' || key === 'ArrowRight') {
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
			activate(slug, true, true);
			nextTab.focus();
		}
	}

	function onMediaChange() {
		setTablistOrientation();
	}

	root.classList.add('ogc-pricing--tabs');

	for (var k = 0; k < tabs.length; k++) {
		tabs[k].addEventListener('click', onTabClick);
		tabs[k].addEventListener('keydown', onTabKeydown);
	}

	window.addEventListener('hashchange', function () {
		var slug = getSlugFromHash();
		if (slug) {
			activate(slug, false, false);
		}
	});

	if (typeof desktopQuery.addEventListener === 'function') {
		desktopQuery.addEventListener('change', onMediaChange);
	} else if (typeof desktopQuery.addListener === 'function') {
		desktopQuery.addListener(onMediaChange);
	}

	setTablistOrientation();
	activate(getSlugFromHash() || tabs[0].getAttribute('data-ogc-pricing-tab'), false, false);
})();
