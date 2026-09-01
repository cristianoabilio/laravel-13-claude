/*
Accept / reject an appointment request (AJAX) on the doctor "Requests" page.
The links previously targeted undefined #accept_appointment / #cancel_appointment
modals; there is no confirmation step here, just an immediate action with toast
feedback, matching the pattern already used for favoriting a doctor.
*/

(function ($) {
	"use strict";

	function updateAppointmentStatus(appointmentId, action) {
		var token = $('meta[name="csrf-token"]').attr('content');

		return $.ajax({
			url: '/doctor/requests/' + appointmentId + '/' + action,
			method: 'PATCH',
			headers: {
				'X-CSRF-TOKEN': token,
				'Accept': 'application/json'
			}
		});
	}

	// Bootstrap's Toast component ships in bootstrap.bundle.min.js, already
	// loaded on every page this script runs on - no extra library needed.
	function showRequestToast(message, variant) {
		if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
			return;
		}

		var $container = $('#request-toast-container');
		if (!$container.length) {
			$container = $('<div id="request-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>')
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

	function handleAction(e, action, variant) {
		e.preventDefault();
		e.stopImmediatePropagation();

		var $link = $(this);
		var $card = $link.closest('.appointment-wrap');

		updateAppointmentStatus($link.data('appointment-id'), action)
			.done(function (response) {
				showRequestToast(response.message, variant);
				$card.fadeOut(300, function () {
					$(this).remove();
				});
			})
			.fail(function (xhr) {
				var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
				showRequestToast(message, 'secondary');
			});
	}

	$(document).on('click', '.accept-link[data-appointment-id]', function (e) {
		handleAction.call(this, e, 'accept', 'success');
	});

	$(document).on('click', '.reject-link[data-appointment-id]', function (e) {
		handleAction.call(this, e, 'reject', 'secondary');
	});

})(jQuery);
