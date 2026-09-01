/*
Accept / reject an appointment request on the doctor "Requests" page. Each
click opens a shared confirmation modal (#accept_appointment / #cancel_appointment
- one instance per page, not per card) before the AJAX call actually runs,
mirroring the shared "removeGalleryImageModal" pattern in profile-settings.js:
the triggering link's appointment id/card are stashed in module state, the
modal is shown manually, and its own confirm button fires the request.
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

	function performAction(appointmentId, $card, action, variant) {
		if (!appointmentId || !$card || !$card.length) {
			return;
		}

		updateAppointmentStatus(appointmentId, action)
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

	function hideModal(modalId) {
		var modalEl = document.getElementById(modalId);
		if (!modalEl) {
			return;
		}

		var modal = bootstrap.Modal.getInstance(modalEl);
		if (modal) {
			modal.hide();
		}
	}

	function showModal(modalId) {
		var modalEl = document.getElementById(modalId);
		if (modalEl) {
			bootstrap.Modal.getOrCreateInstance(modalEl).show();
		}
	}

	var pendingAppointmentId = null;
	var $pendingCard = null;

	$(document).on('click', '.accept-link[data-appointment-id]', function (e) {
		e.preventDefault();

		pendingAppointmentId = $(this).data('appointment-id');
		$pendingCard = $(this).closest('.appointment-wrap');

		showModal('accept_appointment');
	});

	$(document).on('click', '.reject-link[data-appointment-id]', function (e) {
		e.preventDefault();

		pendingAppointmentId = $(this).data('appointment-id');
		$pendingCard = $(this).closest('.appointment-wrap');

		showModal('cancel_appointment');
	});

	$(document).on('click', '#confirm-accept-btn', function () {
		hideModal('accept_appointment');
		performAction(pendingAppointmentId, $pendingCard, 'accept', 'success');
	});

	$(document).on('click', '#confirm-reject-btn', function () {
		hideModal('cancel_appointment');
		performAction(pendingAppointmentId, $pendingCard, 'reject', 'secondary');
	});

})(jQuery);
