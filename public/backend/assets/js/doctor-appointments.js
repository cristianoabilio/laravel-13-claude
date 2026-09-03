/*
"Start Now" on the doctor Appointments page marks a confirmed appointment as
completed. A shared confirmation modal (#start_appointment) gates the action,
mirroring the accept/reject confirmation flow in doctor-requests.js.
*/

(function ($) {
	"use strict";

	function completeAppointment(appointmentId) {
		var token = $('meta[name="csrf-token"]').attr('content');

		return $.ajax({
			url: '/doctor/appointments/' + appointmentId + '/complete',
			method: 'PATCH',
			headers: {
				'X-CSRF-TOKEN': token,
				'Accept': 'application/json'
			}
		});
	}

	function showAppointmentToast(message, variant) {
		if (typeof bootstrap === 'undefined' || !bootstrap.Toast) {
			return;
		}

		var $container = $('#appointment-toast-container');
		if (!$container.length) {
			$container = $('<div id="appointment-toast-container" class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080;"></div>')
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

	$(document).on('click', '.start-link[data-appointment-id]', function (e) {
		e.preventDefault();

		pendingAppointmentId = $(this).data('appointment-id');
		$pendingCard = $(this).closest('.appointment-wrap');

		showModal('start_appointment');
	});

	$(document).on('click', '#confirm-start-btn', function () {
		hideModal('start_appointment');

		if (!pendingAppointmentId || !$pendingCard || !$pendingCard.length) {
			return;
		}

		completeAppointment(pendingAppointmentId)
			.done(function (response) {
				showAppointmentToast(response.message, 'success');
				$pendingCard.fadeOut(300, function () {
					$(this).remove();
				});
			})
			.fail(function (xhr) {
				var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Something went wrong. Please try again.';
				showAppointmentToast(message, 'secondary');
			});
	});

})(jQuery);
