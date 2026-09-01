/*
Favorite a doctor (AJAX). Wires three different heart-button styles used
across the theme, all identified by a shared [data-doctor-id] attribute:
  - ".fav-icon"      - homepage / doctor listing card overlay heart, and the
    doctor details page's heart icon among the share/link buttons.
  - ".favourite-btn" - the patient "Favourites" dashboard page heart, which
    removes the card from the list once unfavorited.
Both classes already have a purely-visual toggleClass() handler in script.js;
this file is loaded before script.js so its handlers bind first and can call
stopImmediatePropagation() to take full control once the server confirms the
real state, instead of guessing optimistically.
*/

(function ($) {
	"use strict";

	function toggleFavorite(doctorId) {
		var token = $('meta[name="csrf-token"]').attr('content');

		return $.ajax({
			url: '/doctor/' + doctorId + '/favorite',
			method: 'POST',
			headers: {
				'X-CSRF-TOKEN': token,
				'Accept': 'application/json'
			}
		});
	}

	function redirectGuestToLogin() {
		// A guest (or a session that has expired) gets redirected to the
		// login page by the server; since that response isn't the JSON this
		// request expected, it always surfaces here as a failure regardless
		// of the underlying status code - so any failure sends them there.
		window.location.href = '/login';
	}

	// Bootstrap's Toast component ships in bootstrap.bundle.min.js, already
	// loaded on every page this script runs on - no extra library needed.
	function showFavoriteToast(message, variant) {
		if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
			return;
		}

		var $container = $('#favorite-toast-container');
		if (!$container.length) {
			$container = $('<div id="favorite-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>')
				.appendTo('body');
		}

		var $toast = $(
			'<div class="toast align-items-center text-bg-' + variant + ' border-0" role="status" aria-live="polite" aria-atomic="true">' +
				'<div class="d-flex">' +
					'<div class="toast-body"></div>' +
					'<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
				'</div>' +
			'</div>'
		);
		$toast.find('.toast-body').text(message);
		$container.append($toast);

		var toast = new bootstrap.Toast($toast[0], { delay: 3000 });
		$toast.on('hidden.bs.toast', function () {
			$(this).remove();
		});
		toast.show();
	}

	// Homepage / doctor listing card heart, and the doctor details page heart

	$(document).on('click', '.fav-icon[data-doctor-id]', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var $icon = $(this);

		toggleFavorite($icon.data('doctor-id'))
			.done(function (response) {
				$icon.toggleClass('selected', response.favorited);
				showFavoriteToast(
					response.favorited ? 'Doctor added to favorites.' : 'Doctor removed from favorites.',
					response.favorited ? 'success' : 'secondary'
				);
			})
			.fail(redirectGuestToLogin);
	});

	// Patient "Favourites" dashboard page heart - always removes the card,
	// since a doctor un-favorited from this page no longer belongs on it.

	$(document).on('click', '.favourite-btn[data-doctor-id]', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var $btn = $(this);
		var $card = $btn.closest('.col-lg-4');

		toggleFavorite($btn.data('doctor-id'))
			.done(function (response) {
				if (!response.favorited) {
					showFavoriteToast('Doctor removed from favorites.', 'secondary');
					$card.fadeOut(300, function () {
						$(this).remove();
					});
				}
			})
			.fail(redirectGuestToLogin);
	});

})(jQuery);
