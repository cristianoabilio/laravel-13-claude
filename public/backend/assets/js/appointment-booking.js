/*
Doctor appointment booking wizard - connects the existing Doccure wizard
markup to real backend data (services, clinics, slots) and validates each
step before the generic fade-transition handler in script.js is allowed
to advance. Guarded throughout with .length checks so it's a safe no-op
on every page that doesn't render the booking wizard.
*/

(function ($) {
	"use strict";

	if (!$('.booking-widget.multistep-form').length) {
		return;
	}

	var config = window.BOOKING_CONFIG || {};
	var $form = $('#booking-form');
	var $fieldsets = $form.find('> fieldset, .booking-widget > fieldset');

	function selectedServices() {
		var services = [];
		$('.service-checkbox:checked').each(function () {
			services.push({
				id: $(this).val(),
				name: $(this).data('name'),
				price: parseFloat($(this).data('price')),
				duration: parseInt($(this).data('duration'), 10)
			});
		});
		return services;
	}

	function totalDuration(services) {
		return services.reduce(function (sum, s) { return sum + s.duration; }, 0);
	}

	function totalPrice(services) {
		return services.reduce(function (sum, s) { return sum + s.price; }, 0);
	}

	function money(value) {
		return '$' + value.toFixed(2);
	}

	function escapeHtml(value) {
		return $('<div>').text(value == null ? '' : value).html();
	}

	function currentFieldsetIndex() {
		var index = 0;
		$fieldsets.each(function (i) {
			if ($(this).is(':visible')) {
				index = i;
			}
		});
		return index;
	}

	// Step 1: Speciality filter + service selection

	$(document).on('change', '#speciality-filter', function () {
		var specialityId = $(this).val();
		$('.service-row').hide().each(function () {
			if (String($(this).data('speciality-id')) === String(specialityId)) {
				$(this).show();
			}
		});
	});
	$('#speciality-filter').trigger('change');

	// Step 2: Appointment type toggles clinic list / home visit address

	$(document).on('change', 'input[name="appointment_type"]', function () {
		var type = $(this).val();
		$('#clinics-path').toggle(type === 'clinic');
		$('#home-visit-address-wrap').toggle(type === 'home_visit');
	});

	// Step 3: Inline calendar + AJAX slot loading

	if ($('#datetimepickershow').length) {
		$('#datetimepickershow').datetimepicker({
			inline: true,
			sideBySide: true,
			format: 'DD-MM-YYYY',
			minDate: moment(),
			daysOfWeekDisabled: config.closedWeekdays || [],
			icons: {
				up: "fas fa-angle-up",
				down: "fas fa-angle-down",
				next: 'fas fa-angle-right',
				previous: 'fas fa-angle-left'
			}
		}).on('dp.change', function (e) {
			$('#appointment_date').val(e.date ? e.date.format('YYYY-MM-DD') : '');
			$('input[name="start_time"]').prop('checked', false);
			loadSlots();
		});
	}

	function loadSlots() {
		var $container = $('#slots-container');
		var services = selectedServices();
		var date = $('#appointment_date').val();

		if (!services.length || !date) {
			$container.html('<p class="text-muted">Select a date to see available times.</p>');
			return;
		}

		$container.html('<p class="text-muted">Loading available times&hellip;</p>');

		$.ajax({
			url: config.slotsUrl,
			method: 'GET',
			data: {
				doctor_service_ids: services.map(function (s) { return s.id; }),
				date: date
			},
			success: function (response) {
				renderSlots(response.slots || []);
			},
			error: function () {
				$container.html('<p class="text-danger">Unable to load available times. Please try another date.</p>');
			}
		});
	}

	function renderSlots(slots) {
		var $container = $('#slots-container');

		if (!slots.length) {
			$container.html('<p class="text-muted">No available times on this date. Please choose another date.</p>');
			return;
		}

		var groups = { Morning: [], Afternoon: [], Evening: [] };
		slots.forEach(function (slot) {
			var hour = parseInt(slot.time.split(':')[0], 10);
			if (hour < 12) {
				groups.Morning.push(slot);
			} else if (hour < 17) {
				groups.Afternoon.push(slot);
			} else {
				groups.Evening.push(slot);
			}
		});

		var html = '';
		$.each(groups, function (label, groupSlots) {
			if (!groupSlots.length) {
				return;
			}

			html += '<div class="book-title"><h6 class="fs-14 mb-2">' + label + '</h6></div><div class="token-slot mt-2 mb-2">';
			groupSlots.forEach(function (slot) {
				var disabled = slot.available ? '' : 'disabled';
				html += '<div class="form-check-inline visits me-0">' +
					'<label class="visit-btns">' +
					'<input type="radio" class="form-check-input" name="start_time" value="' + slot.time + '" ' + disabled + '>' +
					'<span class="visit-rsn">' + slot.label + '</span>' +
					'</label></div>';
			});
			html += '</div>';
		});

		$container.html(html);
	}

	$(document).on('change', '.service-checkbox', function () {
		if ($('#appointment_date').val()) {
			loadSlots();
		}
	});

	// Step 4: prefill patient info once (in case the user navigates back and forth)

	// Booking summary, refreshed every time the wizard advances

	function refreshSummary() {
		var services = selectedServices();
		var duration = totalDuration(services);
		var subtotal = totalPrice(services);
		var appointmentType = $('input[name="appointment_type"]:checked');
		var typeLabel = appointmentType.length ? appointmentType.closest('.radio-select').find('.service-title').text() : '-';
		var date = $('#appointment_date').val();
		var start = $('input[name="start_time"]:checked');
		var dateTimeLabel = (date && start.length)
			? start.next('.visit-rsn').text() + ', ' + moment(date).format('DD MMM YYYY')
			: '-';

		if (services.length) {
			$('.summary-services').html(services.map(function (s) {
				return '<span class="d-block">' + escapeHtml(s.name) + '</span>';
			}).join(''));
		} else {
			$('.summary-services').text('-');
		}
		$('.summary-duration').text(duration ? (duration + ' Mins') : '-');
		$('.summary-datetime').text(dateTimeLabel);
		$('.summary-type').text(typeLabel);

		var clinic = $('input[name="clinic_id"]:checked');
		$('.summary-clinic-row').toggle(appointmentType.val() === 'clinic');
		$('.summary-clinic').text(clinic.length ? clinic.closest('.service-item').find('.service-title').text() : '-');

		$('.summary-line-services').empty();
		services.forEach(function (s) {
			$('.summary-line-services').append(
				'<div class="d-flex align-items-center flex-wrap rpw-gap-2 justify-content-between mb-2">' +
				'<p class="mb-0">' + escapeHtml(s.name) + '</p><span class="fw-medium d-block">' + money(s.price) + '</span></div>'
			);
		});
		$('.summary-total').text(money(subtotal));

		// Keep hidden inputs backing the visible selects/summary in sync.
		$('input[name="appointment_date"]').val(date);
	}

	// Step validation - runs before script.js's fade-transition handler because
	// this file is loaded first, so its "change" binding order wins; blocking
	// via stopImmediatePropagation prevents the later handler from firing.
	$(document).on('click', '.next_btns', function (e) {
		var index = currentFieldsetIndex();
		var error = null;

		if (index === 0 && selectedServices().length === 0) {
			error = 'Please select at least one service to continue.';
		} else if (index === 1) {
			var type = $('input[name="appointment_type"]:checked').val();
			if (!type) {
				error = 'Please select an appointment type.';
			} else if (type === 'clinic' && !$('input[name="clinic_id"]:checked').length) {
				error = 'Please select a clinic.';
			} else if (type === 'home_visit' && !$('input[name="home_visit_address"]').val()) {
				error = 'Please enter your address for the home visit.';
			}
		} else if (index === 2) {
			if (!$('#appointment_date').val() || !$('input[name="start_time"]:checked').length) {
				error = 'Please select a date and time slot.';
			}
		} else if (index === 3) {
			var requiredFields = ['first_name', 'last_name', 'phone', 'email'];
			for (var i = 0; i < requiredFields.length; i++) {
				if (!$('[name="' + requiredFields[i] + '"]').val()) {
					error = 'Please fill in all required patient information fields.';
					break;
				}
			}
		}

		if (error) {
			e.preventDefault();
			e.stopImmediatePropagation();
			showWizardError(error);
			return false;
		}

		hideWizardError();
		refreshSummary();
	});

	function showWizardError(message) {
		var $alert = $('#wizard-error');
		if (!$alert.length) {
			$alert = $('<div id="wizard-error" class="alert alert-danger mt-3"></div>');
			$('.booking-widget').before($alert);
		}
		$alert.text(message).show();
	}

	function hideWizardError() {
		$('#wizard-error').hide();
	}

	// Payment tabs: PayPal and Stripe are disabled for now (mock credit card only)

	$('#pills-profile-tab, #pills-contact-tab').addClass('disabled').attr({
		'aria-disabled': 'true',
		tabindex: '-1',
		title: 'Coming soon'
	}).on('click', function (e) {
		e.preventDefault();
		e.stopImmediatePropagation();
	});

	// Lightweight card field formatting (no external library required)

	$(document).on('input', 'input[name="card_number"]', function () {
		var digits = $(this).val().replace(/\D/g, '').slice(0, 19);
		$(this).val(digits.replace(/(.{4})/g, '$1 ').trim());
	});

	$(document).on('input', 'input[name="card_expiry"]', function () {
		var digits = $(this).val().replace(/\D/g, '').slice(0, 4);
		if (digits.length >= 3) {
			digits = digits.slice(0, 2) + '/' + digits.slice(2);
		}
		$(this).val(digits);
	});

	$(document).on('input', 'input[name="card_cvv"]', function () {
		$(this).val($(this).val().replace(/\D/g, '').slice(0, 4));
	});

})(jQuery);
