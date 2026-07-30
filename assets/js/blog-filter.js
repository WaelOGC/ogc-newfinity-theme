/**
 * OGC NewFinity — Blog & Insights category + pagination AJAX filter.
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
	var paginationWrap = document.getElementById('ogc-blog-pagination-wrap');
	if (!filters || !wrap) {
		return;
	}

	var buttons = filters.querySelectorAll('.ogc-blog-filter');
	var requestId = 0;
	var fadeMs = 180;
	var currentCategory = 'all';
	var currentPage = 1;

	function readUrlState() {
		var category = 'all';
		var page = 1;
		try {
			var params = new URL(window.location.href).searchParams;
			category = params.get('category') || 'all';
			page = Math.max(1, parseInt(params.get('paged') || '1', 10) || 1);
		} catch (e) {
			category = 'all';
			page = 1;
		}
		return { category: category, page: page };
	}

	function setActive(slug) {
		for (var i = 0; i < buttons.length; i++) {
			var btn = buttons[i];
			var isActive = btn.getAttribute('data-category') === slug;
			btn.classList.toggle('is-active', isActive);
			btn.setAttribute('aria-pressed', isActive ? 'true' : 'false');
		}
	}

	function updateUrl(slug, page) {
		try {
			var url = new URL(window.location.href);
			if (!slug || slug === 'all') {
				url.searchParams.delete('category');
			} else {
				url.searchParams.set('category', slug);
			}
			if (!page || page <= 1) {
				url.searchParams.delete('paged');
			} else {
				url.searchParams.set('paged', String(page));
			}
			window.history.pushState(
				{ category: slug || 'all', page: page || 1 },
				'',
				url.pathname + url.search + url.hash
			);
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
		if (paginationWrap) {
			paginationWrap.classList.toggle('is-loading', busy);
		}
	}

	function setFading(fading) {
		wrap.classList.toggle('is-fading', fading);
		if (paginationWrap) {
			paginationWrap.classList.toggle('is-fading', fading);
		}
	}

	function scrollToGrid() {
		try {
			wrap.scrollIntoView({ behavior: 'smooth', block: 'start' });
		} catch (e) {
			wrap.scrollIntoView(true);
		}
	}

	function swapContent(html, paginationHtml, shouldScroll) {
		setFading(true);
		window.setTimeout(function () {
			wrap.innerHTML = html;
			if (paginationWrap) {
				paginationWrap.innerHTML = typeof paginationHtml === 'string' ? paginationHtml : '';
			}
			setFading(false);
			setBusy(false);
			runOrphanControl();
			if (shouldScroll) {
				scrollToGrid();
			}
		}, fadeMs);
	}

	function fetchPosts(slug, page, options) {
		options = options || {};
		var id = ++requestId;
		var nextPage = Math.max(1, parseInt(page, 10) || 1);

		currentCategory = slug || 'all';
		currentPage = nextPage;

		setBusy(true);
		setFading(true);

		var body = new FormData();
		body.append('action', 'ogc_filter_blog_posts');
		body.append('nonce', cfg.nonce);
		body.append('category', currentCategory);
		body.append('page', String(currentPage));

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
					setFading(false);
					return;
				}
				if (typeof json.data.current_page === 'number') {
					currentPage = json.data.current_page;
				}
				swapContent(json.data.html, json.data.pagination_html || '', !!options.scroll);
			})
			.catch(function () {
				if (id !== requestId) {
					return;
				}
				setBusy(false);
				setFading(false);
			});
	}

	filters.addEventListener('click', function (event) {
		var btn = event.target.closest('.ogc-blog-filter');
		if (!btn || !filters.contains(btn)) {
			return;
		}

		var slug = btn.getAttribute('data-category') || 'all';
		if (btn.classList.contains('is-active') && currentPage === 1) {
			return;
		}

		setActive(slug);
		updateUrl(slug, 1);
		fetchPosts(slug, 1, { scroll: false });
	});

	document.addEventListener('click', function (event) {
		var pageBtn = event.target.closest('.ogc-blog-pagination [data-page]');
		if (!pageBtn) {
			return;
		}
		if (paginationWrap && !paginationWrap.contains(pageBtn)) {
			return;
		}

		event.preventDefault();
		var page = Math.max(1, parseInt(pageBtn.getAttribute('data-page') || '1', 10) || 1);
		if (page === currentPage) {
			return;
		}

		updateUrl(currentCategory, page);
		fetchPosts(currentCategory, page, { scroll: true });
	});

	window.addEventListener('popstate', function (event) {
		var slug = 'all';
		var page = 1;
		if (event.state && event.state.category) {
			slug = event.state.category;
			page = Math.max(1, parseInt(event.state.page, 10) || 1);
		} else {
			var state = readUrlState();
			slug = state.category;
			page = state.page;
		}
		setActive(slug);
		fetchPosts(slug, page, { scroll: false });
	});

	/* Sync JS state with server-rendered URL params. */
	(function initFromUrl() {
		var state = readUrlState();
		currentCategory = state.category;
		currentPage = state.page;
		setActive(currentCategory);
	})();
})();
