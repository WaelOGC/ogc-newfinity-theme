/**
 * OGC NewFinity — Blog & Insights category AJAX filter.
 * Enqueued only on the posts index (home.php / /blog/).
 */
(function () {
	'use strict';

	var cfg = window.ogcBlogFilter;
	if (!cfg || !cfg.ajaxUrl || !cfg.nonce) {
		return;
	}

	var filters = document.querySelector('.ogc-blog-filters');
	var wrap = document.getElementById('ogc-blog-grid-wrap');
	if (!filters || !wrap) {
		return;
	}

	var buttons = filters.querySelectorAll('.ogc-blog-filter');
	var requestId = 0;
	var fadeMs = 180;

	function setActive(slug) {
		for (var i = 0; i < buttons.length; i++) {
			var btn = buttons[i];
			var isActive = btn.getAttribute('data-category') === slug;
			btn.classList.toggle('is-active', isActive);
			btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		}
	}

	function updateUrl(slug) {
		try {
			var url = new URL(window.location.href);
			if (!slug || slug === 'all') {
				url.searchParams.delete('category');
			} else {
				url.searchParams.set('category', slug);
			}
			window.history.pushState({ category: slug || 'all' }, '', url.pathname + url.search + url.hash);
		} catch (e) {
			/* Older browsers — ignore URL sync failure. */
		}
	}

	function runOrphanControl() {
		if (typeof window.ogcOrphanControlRerun === 'function') {
			window.ogcOrphanControlRerun();
			return;
		}
		/* Fallback: dispatch resize so orphan-control's listener re-runs. */
		window.dispatchEvent(new Event('resize'));
	}

	function setBusy(busy) {
		wrap.setAttribute('aria-busy', busy ? 'true' : 'false');
		wrap.classList.toggle('is-loading', busy);
	}

	function swapHtml(html) {
		wrap.classList.add('is-fading');
		window.setTimeout(function () {
			wrap.innerHTML = html;
			wrap.classList.remove('is-fading');
			setBusy(false);
			runOrphanControl();
		}, fadeMs);
	}

	function fetchPosts(slug) {
		var id = ++requestId;
		setBusy(true);
		wrap.classList.add('is-fading');

		var body = new FormData();
		body.append('action', 'ogc_filter_blog_posts');
		body.append('nonce', cfg.nonce);
		body.append('category', slug || 'all');

		fetch(cfg.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			body: body
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				if (id !== requestId) {
					return;
				}
				if (!json || !json.success || !json.data || typeof json.data.html !== 'string') {
					setBusy(false);
					wrap.classList.remove('is-fading');
					return;
				}
				swapHtml(json.data.html);
			})
			.catch(function () {
				if (id !== requestId) {
					return;
				}
				setBusy(false);
				wrap.classList.remove('is-fading');
			});
	}

	filters.addEventListener('click', function (event) {
		var btn = event.target.closest('.ogc-blog-filter');
		if (!btn || !filters.contains(btn)) {
			return;
		}

		var slug = btn.getAttribute('data-category') || 'all';
		if (btn.classList.contains('is-active')) {
			return;
		}

		setActive(slug);
		updateUrl(slug);
		fetchPosts(slug);
	});

	window.addEventListener('popstate', function (event) {
		var slug = 'all';
		if (event.state && event.state.category) {
			slug = event.state.category;
		} else {
			try {
				slug = new URL(window.location.href).searchParams.get('category') || 'all';
			} catch (e) {
				slug = 'all';
			}
		}
		setActive(slug);
		fetchPosts(slug);
	});
})();
