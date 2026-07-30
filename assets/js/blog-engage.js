/**
 * OGC NewFinity — single-post like + share controls.
 * Enqueued only on singular posts.
 */
(function () {
	'use strict';

	var cfg = window.ogcBlogEngage;
	if (!cfg || !cfg.ajaxUrl || !cfg.nonce) {
		return;
	}

	var root = document.querySelector('.ogc-engage');
	if (!root) {
		return;
	}

	var postId = root.getAttribute('data-post-id');
	var likeBtn = root.querySelector('[data-ogc-like]');
	var countEl = root.querySelector('[data-ogc-like-count]');
	var labelEl = root.querySelector('.ogc-engage__like-label');
	var copyBtn = root.querySelector('[data-ogc-copy-link]');

	function cookieName(id) {
		return 'ogc_liked_' + id;
	}

	function hasLikedCookie(id) {
		return document.cookie.split(';').some(function (part) {
			return part.trim().indexOf(cookieName(id) + '=') === 0;
		});
	}

	function setLikedCookie(id) {
		var maxAge = 365 * 24 * 60 * 60;
		document.cookie = cookieName(id) + '=1; path=/; max-age=' + maxAge + '; SameSite=Lax';
	}

	function markLiked(count) {
		if (!likeBtn) {
			return;
		}
		likeBtn.classList.add('is-liked');
		likeBtn.setAttribute('aria-pressed', 'true');
		likeBtn.setAttribute('aria-label', cfg.i18n && cfg.i18n.liked ? cfg.i18n.liked : 'Liked');
		likeBtn.disabled = true;
		if (countEl && typeof count === 'number') {
			countEl.textContent = String(count);
		}
		if (labelEl) {
			labelEl.textContent = cfg.i18n && cfg.i18n.likedLabel ? cfg.i18n.likedLabel : 'Liked';
		}
	}

	if (likeBtn && postId && hasLikedCookie(postId)) {
		markLiked(null);
	}

	if (likeBtn && postId) {
		likeBtn.addEventListener('click', function () {
			if (likeBtn.disabled || likeBtn.classList.contains('is-liked') || hasLikedCookie(postId)) {
				markLiked(null);
				return;
			}

			likeBtn.disabled = true;

			var body = new FormData();
			body.append('action', 'ogc_like_post');
			body.append('nonce', cfg.nonce);
			body.append('post_id', postId);

			fetch(cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: body
			})
				.then(function (res) {
					return res.json();
				})
				.then(function (json) {
					if (!json || !json.success || !json.data) {
						likeBtn.disabled = false;
						return;
					}
					setLikedCookie(postId);
					markLiked(typeof json.data.count === 'number' ? json.data.count : null);
				})
				.catch(function () {
					likeBtn.disabled = false;
				});
		});
	}

	function showToast(btn) {
		var toast = btn.querySelector('[data-ogc-copy-toast]');
		if (!toast) {
			return;
		}
		toast.hidden = false;
		toast.classList.add('is-visible');
		window.setTimeout(function () {
			toast.classList.remove('is-visible');
			toast.hidden = true;
		}, 1600);
	}

	if (copyBtn) {
		copyBtn.addEventListener('click', function () {
			var url = copyBtn.getAttribute('data-url') || window.location.href;
			var done = function () {
				showToast(copyBtn);
			};

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(url).then(done).catch(function () {
					fallbackCopy(url, done);
				});
			} else {
				fallbackCopy(url, done);
			}
		});
	}

	function fallbackCopy(text, cb) {
		var ta = document.createElement('textarea');
		ta.value = text;
		ta.setAttribute('readonly', '');
		ta.style.position = 'absolute';
		ta.style.left = '-9999px';
		document.body.appendChild(ta);
		ta.select();
		try {
			document.execCommand('copy');
			cb();
		} catch (e) {
			/* ignore */
		}
		document.body.removeChild(ta);
	}
})();
