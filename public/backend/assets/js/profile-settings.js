/*
Author       : Dreamstechnologies
Template Name: Doccure - Bootstrap Template
Version      : 1.0
*/

(function($) {
    "use strict";

	// Year of Experience Mask (2 digits, e.g. "5", "12")

	function applyYearsMask() {
		if ($.fn.mask) {
			$('.years-mask').mask('99');
		}
	}

	applyYearsMask();

	// Profile Photo Preview

	$(document).on('change', '.change-avatar .upload', function (e) {
		var file = e.target.files && e.target.files[0];

		if (!file) {
			return;
		}

		var reader = new FileReader();

		reader.onload = function (event) {
			var $wrap = $('.change-avatar .profile-img');
			var $img = $wrap.find('img');

			if ($img.length === 0) {
				$img = $('<img>').attr('alt', 'Profile Preview');
				$wrap.empty().append($img);
			}

			$img.attr('src', event.target.result);
		};

		reader.readAsDataURL(file);
	});

	// Known Languages Save (independent of the full profile form)

	$(document).on('click', '.known-languages-save', function (e) {
		e.preventDefault();

		var $btn = $(this);
		var $input = $btn.closest('.input-block').find('.input-tags');
		var $feedback = $btn.closest('.form-wrap').find('.known-languages-feedback');
		var token = $('meta[name="csrf-token"]').attr('content');

		$feedback.removeClass('text-danger text-success').text('Saving...');

		$.ajax({
			url: $btn.data('url'),
			method: 'PATCH',
			headers: {
				'X-CSRF-TOKEN': token,
				'Accept': 'application/json'
			},
			data: {
				known_languages: $input.val()
			},
			success: function (response) {
				$feedback.addClass('text-success').text(response.message);
			},
			error: function (xhr) {
				var message = (xhr.responseJSON && xhr.responseJSON.message) || 'Unable to save known languages.';
				$feedback.addClass('text-danger').text(message);
			}
		});
	});

	// Pricing Options Show
	
	$('#pricing_select input[name="rating_option"]').on('click', function() {
		if ($(this).val() == 'price_free') {
			$('#custom_price_cont').hide();
		}
		if ($(this).val() == 'custom_price') {
			$('#custom_price_cont').show();
		}
		else {
		}
	});
	
	// Education Add More
	
    $(".education-info").on('click','.trash', function () {
		$(this).closest('.education-cont').remove();
		return false;
    });

    $(".add-education").on('click', function () {
		
		var educationcontent = '<div class="row education-cont">' +
			'<div class="col-12 col-md-10 col-lg-11">' +
				'<div class="row">' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>Degree</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>College/Institute</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>Year of Completion</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-2 col-lg-1"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>' +
		'</div>';
		
        $(".education-info").append(educationcontent);
        return false;
    });
	
	// Experience Add More
	
    $(".experience-info").on('click','.trash', function () {
		$(this).closest('.experience-cont').remove();
		return false;
    });

    $(".add-experience").on('click', function () {
		
		var experiencecontent = '<div class="row experience-cont">' +
			'<div class="col-12 col-md-10 col-lg-11">' +
				'<div class="row">' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>Hospital Name</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>From</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>To</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
					'<div class="col-12 col-md-6 col-lg-4">' +
						'<div class="mb-3">' +
							'<label>Designation</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-2 col-lg-1"><label class="d-md-block d-sm-none d-none">&nbsp;</label><a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a></div>' +
		'</div>';
		
        $(".experience-info").append(experiencecontent);
        return false;
    });
	
	// Awards Add More
	
    $(".awards-info").on('click','.trash', function () {
		$(this).closest('.awards-cont').remove();
		return false;
    });

    $(".add-award").on('click', function () {

        var regcontent = '<div class="row awards-cont">' +
			'<div class="col-12 col-md-5">' +
				'<div class="mb-3">' +
					'<label>Awards</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-5">' +
				'<div class="mb-3">' +
					'<label>Year</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-2">' +
				'<label class="d-md-block d-sm-none d-none">&nbsp;</label>' +
				'<a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a>' +
			'</div>' +
		'</div>';
		
        $(".awards-info").append(regcontent);
        return false;
    });
	
	// Membership Add More
	
    $(".membership-info").on('click','.trash', function () {
		$(this).closest('.membership-cont').remove();
		return false;
    });

    $(".add-membership").on('click', function () {

        var membershipcontent = '<div class="row membership-cont">' +
			'<div class="col-12 col-md-10 col-lg-5">' +
				'<div class="mb-3">' +
					'<label>Memberships</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-2 col-lg-2">' +
				'<label class="d-md-block d-sm-none d-none">&nbsp;</label>' +
				'<a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a>' +
			'</div>' +
		'</div>';
		
        $(".membership-info").append(membershipcontent);
        return false;
    });
	
	// Registration Add More
	
    $(".registrations-info").on('click','.trash', function () {
		$(this).closest('.reg-cont').remove();
		return false;
    });

    $(".add-reg").on('click', function () {

        var regcontent = '<div class="row reg-cont">' +
			'<div class="col-12 col-md-5">' +
				'<div class="mb-3">' +
					'<label>Registrations</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-5">' +
				'<div class="mb-3">' +
					'<label>Year</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-12 col-md-2">' +
				'<label class="d-md-block d-sm-none d-none">&nbsp;</label>' +
				'<a href="#" class="btn btn-danger trash"><i class="far fa-trash-alt"></i></a>' +
			'</div>' +
		'</div>';
		
        $(".registrations-info").append(regcontent);
        return false;
    });

    // Billing Add More
	
    $(".add-billing-info").on('click','.trash', function () {
		$(this).closest('.bill-cont').remove();
		return false;
    });

    $(".add-bill").on('click', function () {

        var billcontent = '<div class="row bill-cont">' +
			'<div class="col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Title <span class="text-danger">*</span></label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-md-6">' +
				'<div class="d-flex align-items-center">' +
					'<div class="form-wrap w-100">' +
						'<label class="col-form-label">Amount</label>' +
						'<input type="text" class="form-control">' +
					'</div>' +
					'<div class="form-wrap ms-2">' +
						'<label class="col-form-label d-block">&nbsp;</label>' +
						'<a href="#" class="trash">Delete</a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';
		
        $(".add-billing-info").append(billcontent);
        return false;
    });

    // Prescripe Add More
	
    $(".add-prescripe-info").on('click','.trash', function () {
		$(this).closest('.prescripe-cont').remove();
		return false;
    });

    $(".add-prescribe").on('click', function () {

        var prescontent = '<div class="row prescripe-cont">' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Name</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Type</label>' +
					'<select class="select">' +
						'<option>Select</option>' +
						'<option>Visit</option>' +
						'<option>Online</option>' +
					'</select>' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Dosage</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Frequency</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Duration</label>' +
					'<select class="select">' +
						'<option>Select</option>' +
						'<option>1 Month</option>' +
						'<option>1 Day</option>' +
					'</select>' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="d-flex align-items-center">' +
					'<div class="form-wrap w-100">' +
						'<label class="col-form-label">Instruction</label>' +
						'<input type="text" class="form-control">' +
					'</div>' +
					'<div class="form-wrap ms-2">' +
						'<label class="col-form-label d-block">&nbsp;</label>' +
						'<a href="#" class="trash"><i class="fa-solid fa-trash-can"></i></a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';
		
        $(".add-prescripe-info").append(prescontent);

        if ($('.select').length > 0) {
			$('.select').select2({
				minimumResultsForSearch: -1,
				width: '100%'
			});
		}

        return false;
    });

    // Prescripe Add More
	
    $(".add-prescripe-info").on('click','.trash', function () {
		$(this).closest('.prescripe-cont').remove();
		return false;
    });

    $(".add-prescribe").on('click', function () {

        var prescontent = '<div class="row prescripe-cont">' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Name</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Type</label>' +
					'<select class="select">' +
						'<option>Select</option>' +
						'<option>Visit</option>' +
						'<option>Online</option>' +
					'</select>' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Dosage</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Frequency</label>' +
					'<input type="text" class="form-control">' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="form-wrap">' +
					'<label class="col-form-label">Duration</label>' +
					'<select class="select">' +
						'<option>Select</option>' +
						'<option>1 Month</option>' +
						'<option>1 Day</option>' +
					'</select>' +
				'</div>' +
			'</div>' +
			'<div class="col-xl-2 xol-lg-3 col-md-6">' +
				'<div class="d-flex align-items-center">' +
					'<div class="form-wrap w-100">' +
						'<label class="col-form-label">Instruction</label>' +
						'<input type="text" class="form-control">' +
					'</div>' +
					'<div class="form-wrap ms-2">' +
						'<label class="col-form-label d-block">&nbsp;</label>' +
						'<a href="#" class="trash"><i class="fa-solid fa-trash-can"></i></a>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';
		
        $(".add-prescripe-info").append(prescontent);

        if ($('.select').length > 0) {
			$('.select').select2({
				minimumResultsForSearch: -1,
				width: '100%'
			});
		}

        return false;
    });

    // Add Speciality
	
    $(".add-service-info").on('click','.trash', function () {
		$(this).closest('.service-cont').remove();
		return false;
    });

    $(".add-speciality").on('click', function () {

        var servcontent = '<div class="user-accordion-item">' +
			'<a href="#" class="accordion-wrap collapsed" data-bs-toggle="collapse" data-bs-target="#special">Speciality<span>Delete</span></a>' +
			'<div class="accordion-collapse" id="special" data-bs-parent="#list-accord">' +
				'<div class="content-collapse">' +
					'<div class="add-service-info">' +
						'<div class="add-info">' +
							'<div class="row">' +
								'<div class="col-md-4">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Speciality <span class="text-danger">*</span></label>' +
										'<select class="select">' +
											'<option>Select</option>' +
											'<option>Neurology</option>' +
											'<option>Urology</option>' +
										'</select>' +
									'</div>	' +												
								'</div>' +
							'</div>' +
							'<div class="row service-cont">' +
								'<div class="col-md-3">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Service <span class="text-danger">*</span></label>' +
										'<select class="select">' +
											'<option>Select Service</option>' +
											'<option>Surgery</option>' +
											'<option>General Checkup</option>' +
										'</select>' +
									'</div>	' +												
								'</div>' +
								'<div class="col-md-2">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Price ($) <span class="text-danger">*</span></label>' +
										'<input type="text" class="form-control" placeholder="454">' +
									'</div>' +													
								'</div>' +
								'<div class="col-md-7">' +
									'<div class="d-flex align-items-center">' +
										'<div class="form-wrap w-100">' +
											'<label class="col-form-label">About Service</label>' +
											'<input type="text" class="form-control">' +
										'</div>' +
										'<div class="form-wrap ms-2">' +
											'<label class="col-form-label d-block">&nbsp;</label>' +
											'<a href="#" class="trash-icon trash">Delete</a>' +
										'</div>' +												
									'</div>' +													
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="text-end">' +
							'<a href="#" class="add-serv more-item mb-0">Add New Service</a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';
		
        $('.accordions').append(servcontent);

        if ($('.select').length > 0) {
			$('.select').select2({
				minimumResultsForSearch: -1,
				width: '100%'
			});
		}

        return false;
    });

    // Service Add More
	
    $(".add-service-info").on('click','.trash', function () {
		$(this).closest('.service-cont').remove();
		return false;
    });

    $(".add-serv").on('click', function () {

        var servcontent = '<div class="row service-cont">' +
				'<div class="col-md-3">' +
					'<div class="form-wrap">' +
						'<label class="col-form-label">Service <span class="text-danger">*</span></label>' +
						'<select class="select">' +
							'<option>Select Service</option>' +
							'<option>Surgery</option>' +
							'<option>General Checkup</option>' +
						'</select>' +
					'</div>' +													
				'</div>' +
				'<div class="col-md-2">' +
					'<div class="form-wrap">' +
						'<label class="col-form-label">Price ($) <span class="text-danger">*</span></label>' +
						'<input type="text" class="form-control" placeholder="454">' +
					'</div>' +													
				'</div>' +
				'<div class="col-md-7">' +
					'<div class="d-flex align-items-center">' +
						'<div class="form-wrap w-100">' +
							'<label class="col-form-label">About Service</label>' +
							'<input type="text" class="form-control">' +
						'</div>' +
						'<div class="form-wrap ms-2">' +
							'<label class="col-form-label d-block">&nbsp;</label>' +
							'<a href="#" class="trash-icon trash">Delete</a>' +
						'</div>' +										
					'</div>' +													
				'</div>' +	
			'</div>';
		
        $(this).closest(".add-service-info").find('.add-info').append(servcontent);

        if ($('.select').length > 0) {
			$('.select').select2({
				minimumResultsForSearch: -1,
				width: '100%'
			});
		}

        return false;
    });

     // Add Membership
	
    $(".membership-infos").on('click','.trash', function () {
		$(this).closest('.membership-content').remove();
		return false;
    });

    $(".add-membership-info").on('click', function () {

        var index = parseInt($(this).attr('data-next-index'), 10) || 0;

        var membershipcontent = '<div class="row membership-content">' +
			'<div class="col-lg-3 col-md-6">' +
											'<div class="form-wrap">' +
												'<label class="col-form-label">Title <span class="text-danger">*</span></label>' +
												'<input type="text" name="memberships[' + index + '][title]" class="form-control" placeholder="Add Title">' +
											'</div>' +
										'</div>' +
										'<div class="col-lg-9 col-md-6">' +
											'<div class="d-flex align-items-center">' +
												'<div class="form-wrap w-100">' +
													'<label class="col-form-label">About Membership</label>' +
													'<input type="text" name="memberships[' + index + '][description]" class="form-control">' +
												'</div>' +
												'<div class="form-wrap ms-2">' +
													'<label class="col-form-label d-block">&nbsp;</label>' +
													'<a href="javascript:void(0);" class="trash-icon trash">Delete</a>' +
												'</div>' +
											'</div>' +
										'</div>' +
									'</div>';

        $(".membership-infos").append(membershipcontent);
        $(this).attr('data-next-index', index + 1);
        return false;
    });

    // Repeatable accordion sections (Experience, Education, ...): delete/reset/logo removal

    // Delete (persisted rows only): open the row's confirmation modal instead of
    // toggling the accordion collapse it's nested inside.
    $(document).on('click', '.delete-trigger', function (e) {
        e.preventDefault();
        e.stopPropagation();

        var modalEl = document.getElementById($(this).data('target'));
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        return false;
    });

    // Reset (unsaved rows only): clear the row's fields in place rather than
    // submitting/removing it.
    $(document).on('click', '.reset', function (e) {
        e.preventDefault();

        var $row = $(this).closest('.content-collapse');

        $row.find('input[type="text"], textarea').val('');
        $row.find('input[type="checkbox"]').prop('checked', false);
        $row.find('input[type="file"]').val('');
        $row.find('select.select').val('').trigger('change');
        $row.find('.datetimepicker.end-date').prop('disabled', false);
        $row.find('.profile-img').html('<i class="fa-solid fa-file-image"></i>');
        $row.find('.view-img.pending').remove();

        return false;
    });

    // Logo/image "Remove" for rows with nothing persisted yet: just clear the
    // local file selection and preview, no server request.
    $(document).on('click', '.logo-remove-local', function (e) {
        e.preventDefault();

        var $wrap = $(this).closest('.change-avatar');
        $wrap.find('input[type="file"]').val('');
        $wrap.find('.profile-img').html('<i class="fa-solid fa-file-image"></i>');

        return false;
    });

    // "I Currently Working Here" disables and clears the End Date field.
    $(document).on('change', '.currently-working', function () {
        var $endDate = $(this).closest('.content-collapse').find('.end-date');

        if ($(this).is(':checked')) {
            $endDate.val('').prop('disabled', true);
        } else {
            $endDate.prop('disabled', false);
        }
    });

    // Business Hours: "Open on <day>" enables/disables that day's From/To fields.
    $(document).on('change', '.day-open-toggle', function () {
        var $times = $(this).closest('.content-collapse').find('.timepicker1');

        if ($(this).is(':checked')) {
            $times.prop('disabled', false);
        } else {
            $times.val('').prop('disabled', true);
        }
    });

    // Business Hours: unselecting a day's nav pill also unchecks that day's
    // "Open" toggle and disables its From/To fields (script.js already toggled
    // the pill's/day panel's "active" class by the time this delegated handler
    // runs, since direct listeners on the element fire before bubbling reaches
    // document); selecting the pill back on re-enables them the same way.
    $(document).on('click', '.business-nav .tab-link', function () {
        var $day = $('#' + $(this).data('tab'));
        var isActive = $(this).hasClass('active');

        $day.find('.day-open-toggle').prop('checked', isActive).trigger('change');
    });

    $(".add-experiences").on('click', function () {

        var index = parseInt($(this).attr('data-next-index'), 10) || 0;
        var accordionId = 'experience-new-' + index;

        var experienceContent = '<div class="user-accordion-item experience-content">' +
			'<a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#' + accordionId + '">Experience</a>' +
			'<div class="accordion-collapse collapse show" id="' + accordionId + '" data-bs-parent="#list-accord">' +
				'<div class="content-collapse">' +
					'<div class="add-service-info">' +
						'<div class="add-info">' +
							'<div class="row align-items-center">' +
								'<div class="col-md-12">' +
									'<div class="form-wrap mb-2">' +
										'<div class="change-avatar img-upload">' +
											'<div class="profile-img">' +
												'<i class="fa-solid fa-file-image"></i>' +
											'</div>' +
											'<div class="upload-img">' +
												'<h5>Hospital Logo</h5>' +
												'<div class="imgs-load d-flex align-items-center">' +
													'<div class="change-photo">' +
														'Upload New' +
														'<input type="file" name="experiences[' + index + '][hospital_logo]" class="upload" accept="image/jpeg,image/png">' +
													'</div>' +
													'<a href="#" class="upload-remove logo-remove-local">Remove</a>' +
												'</div>' +
												'<p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>' +
											'</div>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Title</label>' +
										'<input type="text" name="experiences[' + index + '][title]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Hospital <span class="text-danger">*</span></label>' +
										'<input type="text" name="experiences[' + index + '][hospital]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Year of Experience <span class="text-danger">*</span></label>' +
										'<input type="text" inputmode="numeric" name="experiences[' + index + '][years_of_experience]" class="form-control years-mask">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Location <span class="text-danger">*</span></label>' +
										'<input type="text" name="experiences[' + index + '][location]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Employement </label>' +
										'<select class="select" name="experiences[' + index + '][employment_type]">' +
											'<option value="">Select</option>' +
											'<option value="full_time">Full Time</option>' +
											'<option value="part_time">Part Time</option>' +
										'</select>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-12">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Job Description <span class="text-danger">*</span></label>' +
										'<textarea class="form-control" rows="3" name="experiences[' + index + '][job_description]"></textarea>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Start Date <span class="text-danger">*</span></label>' +
										'<div class="form-icon">' +
											'<input type="text" name="experiences[' + index + '][start_date]" class="form-control datetimepicker" autocomplete="off">' +
											'<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">End Date <span class="text-danger">*</span></label>' +
										'<div class="form-icon">' +
											'<input type="text" name="experiences[' + index + '][end_date]" class="form-control datetimepicker end-date" autocomplete="off">' +
											'<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">&nbsp;</label>' +
										'<div class="form-check">' +
	   										'<label class="form-check-label">' +
	   											'<input class="form-check-input currently-working" type="checkbox" name="experiences[' + index + '][currently_working]" value="1"> I Currently Working Here' +
	   										'</label>' +
	   									'</div>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="text-end">' +
							'<a href="#" class="reset more-item">Reset</a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(".experience-infos").append(experienceContent);
        $(this).attr('data-next-index', index + 1);
        applyYearsMask();

        if ($('.select').length > 0) {
			$('.select').select2({
				minimumResultsForSearch: -1,
				width: '100%'
			});
		}
		if ($('.datetimepicker').length > 0) {
			$('.datetimepicker').datetimepicker({
				format: 'DD/MM/YYYY',
				icons: {
					up: "fas fa-chevron-up",
					down: "fas fa-chevron-down",
					next: 'fas fa-chevron-right',
					previous: 'fas fa-chevron-left'
				}
			});
		}

        return false;
    });

	// Add Education

    $(".add-educations").on('click', function () {

        var index = parseInt($(this).attr('data-next-index'), 10) || 0;
        var accordionId = 'education-new-' + index;

        var educationContent = '<div class="user-accordion-item education-content">' +
			'<a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#' + accordionId + '">Education</a>' +
			'<div class="accordion-collapse collapse show" id="' + accordionId + '" data-bs-parent="#list-accord">' +
				'<div class="content-collapse">' +
					'<div class="add-service-info">' +
						'<div class="add-info">' +
							'<div class="row align-items-center">' +
								'<div class="col-md-12">' +
									'<div class="form-wrap mb-2">' +
										'<div class="change-avatar img-upload">' +
											'<div class="profile-img">' +
												'<i class="fa-solid fa-file-image"></i>' +
											'</div>' +
											'<div class="upload-img">' +
												'<h5>Logo</h5>' +
												'<div class="imgs-load d-flex align-items-center">' +
													'<div class="change-photo">' +
														'Upload New' +
														'<input type="file" name="educations[' + index + '][logo]" class="upload" accept="image/jpeg,image/png">' +
													'</div>' +
													'<a href="#" class="upload-remove logo-remove-local">Remove</a>' +
												'</div>' +
												'<p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>' +
											'</div>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Name of the institution</label>' +
										'<input type="text" name="educations[' + index + '][institution]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Course</label>' +
										'<input type="text" name="educations[' + index + '][course]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Start Date <span class="text-danger">*</span></label>' +
										'<div class="form-icon">' +
											'<input type="text" name="educations[' + index + '][start_date]" class="form-control datetimepicker" autocomplete="off">' +
											'<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">End Date <span class="text-danger">*</span></label>' +
										'<div class="form-icon">' +
											'<input type="text" name="educations[' + index + '][end_date]" class="form-control datetimepicker" autocomplete="off">' +
											'<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-4 col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">No of Years <span class="text-danger">*</span></label>' +
										'<input type="text" inputmode="numeric" name="educations[' + index + '][no_of_years]" class="form-control years-mask">' +
									'</div>' +
								'</div>' +
								'<div class="col-lg-12">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Description <span class="text-danger">*</span></label>' +
										'<textarea class="form-control" rows="3" name="educations[' + index + '][description]"></textarea>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="text-end">' +
							'<a href="#" class="reset more-item">Reset</a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(".education-infos").append(educationContent);
        $(this).attr('data-next-index', index + 1);
        applyYearsMask();

		if ($('.datetimepicker').length > 0) {
			$('.datetimepicker').datetimepicker({
				format: 'DD/MM/YYYY',
				icons: {
					up: "fas fa-chevron-up",
					down: "fas fa-chevron-down",
					next: 'fas fa-chevron-right',
					previous: 'fas fa-chevron-left'
				}
			});
		}

        return false;
    });

    // Add Experience
	
    $(".awrad-infos").on('click','.trash', function () {
		$(this).closest('.awrad-content').remove();
		return false;
    });

    $(".add-awrads").on('click', function () {

        var membershipcontent = '<div class="awrad-content">' +
			'<div class="user-accordion-item">' +
				'<a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#award">Award<span class="trash">Delete</span></a>' +
				'<div class="accordion-collapse collapse show" id="award" data-bs-parent="#list-accord">' +
					'<div class="content-collapse">' +
						'<div class="add-service-info">' +
							'<div class="add-info">' +
								'<div class="row align-items-center">' +
									'<div class="col-md-6">' +
										'<div class="form-wrap">' +
											'<label class="col-form-label">Award Name</label>' +
											'<input type="text" class="form-control">' +
										'</div>	' +												
									'</div>' +
									'<div class="col-md-6">' +
										'<div class="form-wrap">' +
											'<label class="col-form-label">Year <span class="text-danger">*</span></label>' +
											'<div class="form-icon">' +
												'<input type="text" class="form-control datetimepicker">' +
												'<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>' +
											'</div>' +
										'</div>' +													
									'</div>' +
									'<div class="col-lg-12">' +
										'<div class="form-wrap">' +
											'<label class="col-form-label">Job Description <span class="text-danger">*</span></label>' +
											'<textarea class="form-control" rows="3"></textarea>' +
										'</div>' +													
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="text-end">' +
								'<a href="#" class="reset more-item">Reset</a>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';
		
        $(".awrad-infos").append(membershipcontent);
		if ($('.datetimepicker').length > 0) {
			$('.datetimepicker').datetimepicker({
				format: 'DD/MM/YYYY',
				icons: {
					up: "fas fa-chevron-up",
					down: "fas fa-chevron-down",
					next: 'fas fa-chevron-right',
					previous: 'fas fa-chevron-left'
				}
			});
		}

        return false;
    });


    // Add Insurance
	
    $(".insurance-infos").on('click','.trash', function () {
		$(this).closest('.insurance-content').remove();
		return false;
    });

    $(".add-insurance").on('click', function () {

        var membershipcontent = '<div class="insurance-content">' +
			'<div class="user-accordion-item">' +
				'<a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#insurance">Insurance<span class="trash">Delete</span></a>' +
				'<div class="accordion-collapse collapse show" id="insurance" data-bs-parent="#list-accord">' +
					'<div class="content-collapse">' +
						'<div class="add-service-info">' +
							'<div class="add-info">' +
								'<div class="row align-items-center">' +
									'<div class="col-md-12">' +
										'<div class="form-wrap mb-2">' +
											'<div class="change-avatar img-upload">' +
												'<div class="profile-img">' +
													'<i class="fa-solid fa-file-image"></i>' +
												'</div>' +
												'<div class="upload-img">' +
													'<h5> Logo</h5>' +
													'<div class="imgs-load d-flex align-items-center">' +
														'<div class="change-photo">' +
															'Upload New' + 
															'<input type="file" class="upload">' +
														'</div>' +
														'<a href="#" class="upload-remove">Remove</a>' +
													'</div>' +
													'<p class="form-text">Your Image should Below 4 MB, Accepted format jpg,png,svg</p>' +
												'</div>' +
											'</div>' +
										'</div>' +	
										'<div class="form-wrap">' +
											'<label class="col-form-label">Insurance Name</label>' +
											'<input type="text" class="form-control">' +
										'</div>	' +												
									'</div>' +
								'</div>' +
							'</div>' +
							'<div class="text-end">' +
								'<a href="#" class="reset more-item">Reset</a>' +
							'</div>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>';
		
        $(".insurance-infos").append(membershipcontent);

        return false;
    });

    // Add Clinic

    $(".add-clinics").on('click', function () {

        var index = parseInt($(this).attr('data-next-index'), 10) || 0;
        var accordionId = 'clinic-new-' + index;

        var clinicContent = '<div class="user-accordion-item clinic-content">' +
			'<a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#' + accordionId + '">Clinic</a>' +
			'<div class="accordion-collapse collapse show" id="' + accordionId + '" data-bs-parent="#list-accord">' +
				'<div class="content-collapse">' +
					'<div class="add-service-info">' +
						'<div class="add-info">' +
							'<div class="row align-items-center">' +
								'<div class="col-md-12">' +
									'<div class="form-wrap mb-2">' +
										'<div class="change-avatar img-upload">' +
											'<div class="profile-img">' +
												'<i class="fa-solid fa-file-image"></i>' +
											'</div>' +
											'<div class="upload-img">' +
												'<h5>Logo</h5>' +
												'<div class="imgs-load d-flex align-items-center">' +
													'<div class="change-photo">' +
														'Upload New' +
														'<input type="file" name="clinics[' + index + '][logo]" class="upload" accept="image/jpeg,image/png">' +
													'</div>' +
													'<a href="#" class="upload-remove logo-remove-local">Remove</a>' +
												'</div>' +
												'<p class="form-text">Your Image should Below 4 MB, Accepted format jpg, png.</p>' +
											'</div>' +
										'</div>' +
									'</div>' +
								'</div>' +
								'<div class="col-md-12">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Clinic Name <span class="text-danger">*</span></label>' +
										'<input type="text" name="clinics[' + index + '][name]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Location <span class="text-danger">*</span></label>' +
										'<input type="text" name="clinics[' + index + '][location]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-6">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Address <span class="text-danger">*</span></label>' +
										'<input type="text" name="clinics[' + index + '][address]" class="form-control">' +
									'</div>' +
								'</div>' +
								'<div class="col-md-12">' +
									'<div class="form-wrap">' +
										'<label class="col-form-label">Gallery</label>' +
										'<div class="drop-file">' +
											'<p>Drop files or Click to upload</p>' +
											'<input type="file" name="clinics[' + index + '][gallery][]" class="gallery-input" accept="image/jpeg,image/png" multiple>' +
										'</div>' +
										'<div class="view-imgs"></div>' +
										'<p class="form-text">Images will be resized to 300x300 and processed in the background - they may take a moment to appear.</p>' +
									'</div>' +
								'</div>' +
							'</div>' +
						'</div>' +
						'<div class="text-end">' +
							'<a href="#" class="reset more-item">Reset</a>' +
						'</div>' +
					'</div>' +
				'</div>' +
			'</div>' +
		'</div>';

        $(".clinic-infos").append(clinicContent);
        $(this).attr('data-next-index', index + 1);

        return false;
    });

    // Clinic Gallery: local preview + removable-before-submit selection

    $(document).on('change', '.gallery-input', function () {
        var $input = $(this);
        var $viewImgs = $input.closest('.form-wrap').find('.view-imgs');

        $viewImgs.find('.view-img.pending').remove();

        Array.prototype.forEach.call(this.files, function (file) {
            var reader = new FileReader();

            reader.onload = function (event) {
                var $preview = $('<div class="view-img pending"><img alt="Preview"><a href="#" class="gallery-remove-local">Remove</a></div>');
                $preview.find('img').attr('src', event.target.result);
                $preview.data('file', file);
                $viewImgs.append($preview);
            };

            reader.readAsDataURL(file);
        });
    });

    $(document).on('click', '.gallery-remove-local', function (e) {
        e.preventDefault();

        var $preview = $(this).closest('.view-img');
        var $viewImgs = $preview.closest('.view-imgs');
        var $input = $viewImgs.closest('.form-wrap').find('.gallery-input');

        $preview.remove();

        var dt = new DataTransfer();
        $viewImgs.find('.view-img.pending').each(function () {
            var file = $(this).data('file');
            if (file) {
                dt.items.add(file);
            }
        });
        $input[0].files = dt.files;

        return false;
    });

    // Clinic Gallery: removing an already-uploaded image goes through the shared modal

    $(document).on('click', '.gallery-remove-persisted', function (e) {
        e.preventDefault();

        $('#removeGalleryImageForm').attr('action', $(this).data('url'));

        var modalEl = document.getElementById('removeGalleryImageModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        return false;
    });


   if ($('.dependent-status').length > 0) {
   		$(document).ready(function() {

	   		$('.check').change(function() {
			   $('.status-toggle').addClass('checked');
			    if ($(this).is(':checked')) {
			      $(this).closest('.status-toggle').removeClass('checked');
			    }
			});
  		});
   }
	
})(jQuery);