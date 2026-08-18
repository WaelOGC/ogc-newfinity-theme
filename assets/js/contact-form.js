/**
 * OGC NewFinity — Contact form visitor tracking.
 * Additive amyTrack hooks only; does not change submit/validation behavior.
 *
 * The form POSTs to admin-post.php (no AJAX). Submitted is fired when the
 * existing success notice is shown after redirect. In-flight submit is marked
 * so pagehide during that navigation is not counted as abandon.
 */
(function () {
	'use strict';

	var form = document.querySelector('form.ogc-contact-form');
	if (!form || !form.querySelector('#ogc_name')) {
		return;
	}

	var started = false;
	var submitted = false;
	var inFlight = false;
	var abandonedSent = false;

	function track(eventType) {
		if (window.amyTrack) {
			window.amyTrack(eventType);
		}
	}

	function isTrackableField(el) {
		if (!el || !el.name) {
			return false;
		}
		if (el.name === 'ogc_hp_token' || el.type === 'hidden') {
			return false;
		}
		return true;
	}

	function onStart(event) {
		if (started || !isTrackableField(event.target)) {
			return;
		}
		started = true;
		track('contact_form_started');
	}

	function trackAbandoned() {
		if (!started || submitted || inFlight || abandonedSent) {
			return;
		}
		abandonedSent = true;
		track('contact_form_abandoned');
	}

	form.addEventListener('focusin', onStart);
	form.addEventListener('input', onStart);
	form.addEventListener('change', onStart);

	form.addEventListener('submit', function () {
		inFlight = true;
	});

	if (document.querySelector('.ogc-contact-notice--success')) {
		submitted = true;
		track('contact_form_submitted');
	}

	document.addEventListener('visibilitychange', function () {
		if (document.visibilityState === 'hidden') {
			trackAbandoned();
		}
	});
	window.addEventListener('pagehide', trackAbandoned);
})();
