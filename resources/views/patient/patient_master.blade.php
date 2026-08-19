<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="utf-8">
		<title>Doccure</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta property="og:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta property="og:image" content="assets/img/preview-banner.jpg">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta name="twitter:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="twitter:image" content="assets/img/preview-banner.jpg">

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('backend/assets/img/favicon.png') }}" type="image/x-icon">

		<!-- Apple Touch Icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="assets/img/apple-touch-icon.png">

		<!-- Theme Settings Js -->
		<script src="{{ asset('backend/assets/js/theme-script.js') }}"></script>

		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">

		<!-- Fontawesome CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

		<!-- Iconsax CSS-->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/iconsax.css') }}">

		<!-- Feathericon CSS -->
    	<link rel="stylesheet" href="{{ asset('backend/assets/css/feather.css') }}">

		<!-- Owl carousel CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/owl.carousel.min.css') }}">

		<!-- select CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/select2/css/select2.min.css') }}">

		<!-- Datepicker CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-datetimepicker.min.css') }}">

		<!-- Apex Css -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/apex/apexcharts.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">

	</head>
	<body>

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Header -->
            @include('patient.body.header')

			<!-- Page Content -->
			<div class="content">
				<div class="container">

					<div class="row">

						<!-- Profile Sidebar -->
						@include('patient.body.sidebar')
						<!-- / Profile Sidebar -->

						@yield('patient')
					</div>

				</div>

			</div>
			<!-- /Page Content -->

			<!-- Footer Section -->
			@include('patient.body.footer')
			<!-- /Footer Section -->

		</div>
		<!-- /Main Wrapper -->

		<!-- Add Dependent Modal-->
		<div class="modal fade custom-modals" id="add_dependent">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">Add Dependant</h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<form action="#">
						<div class="add-dependent">
							<div class="modal-body">
								<div class="row">
									<div class="col-md-12">
										<div class="form-wrap pb-0">
											<div class="change-avatar img-upload">
												<div class="profile-img">
													<i class="fa-solid fa-file-image"></i>
												</div>
												<div class="upload-img">
													<h5>Profile Image</h5>
													<div class="imgs-load d-flex align-items-center">
														<div class="change-photo">
															Upload New
															<input type="file" class="upload">
														</div>
														<a href="#" class="upload-remove">Remove</a>
													</div>
													<p>Your Image should Below 4 MB, Accepted format jpg,png,svg</p>
												</div>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-wrap">
											<label class="col-form-label">Name</label>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-wrap">
											<label class="col-form-label">Relationship</label>
											<input type="text" class="form-control">
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-wrap">
											<label class="col-form-label">DOB <span class="text-danger">*</span></label>
											<div class="form-icon">
												<input type="text" class="form-control datetimepicker">
												<span class="icon"><i class="fa-regular fa-calendar-days"></i></span>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-wrap">
											<label class="col-form-label">Select Gender</label>
											<select class="select">
												<option>Select</option>
												<option>Male</option>
												<option>Female</option>
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="modal-btn text-end">
								<a href="#" class="btn btn-gray" data-bs-toggle="modal" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Dependent Modal-->

		<!--View Invoice -->
		<div class="modal fade custom-modals" id="invoice_view">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">View Invoice</h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="prescribe-download">
							<h5>21 Mar  2024</h5>
							<ul>
								<li><a href="javascript:void(0);" class="print-link"><i class="isax isax-printer"></i></a></li>
								<li><a href="#" class="btn btn-md btn-primary-gradient rounded-pill">Download</a></li>
							</ul>
						</div>
						<div class="view-prescribe invoice-content mb-0">
							<div class="invoice-item">
								<div class="row">
									<div class="col-md-6">
										<div class="invoice-logo">
											<img src="{{ asset('backend') }}/assets/img/logo.svg" alt="logo">
										</div>
									</div>
									<div class="col-md-6">
										<p class="invoice-details">
											Invoice No : <span> #INV005</span><br>
											Issued: <span>21 Mar 2024</span>
										</p>
									</div>
								</div>
							</div>

							<!-- Invoice Item -->
							<div class="invoice-item">
								<div class="row">
									<div class="col-md-4">
										<div class="invoice-info">
											<h6 class="customer-text">Billing From</h6>
											<p class="invoice-details invoice-details-two">
												Edalin Hendry <br>
												806 Twin Willow Lane, <br>
												Newyork, USA <br>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="invoice-info">
											<h6 class="customer-text">Billing To</h6>
											<p class="invoice-details invoice-details-two">
												Richard Wilson <br>
												299 Star Trek Drive<br>
												Florida, 32405, USA<br>
											</p>
										</div>
									</div>
									<div class="col-md-4">
										<div class="invoice-info invoice-info2">
											<h6 class="customer-text">Payment Method</h6>
											<p class="invoice-details">
												Debit Card <br>
												XXXXXXXXXXXX-2541<br>
												HDFC Bank<br>
											</p>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<!-- Invoice Item -->
							<div class="invoice-item invoice-table-wrap">
								<div class="row">
									<div class="col-md-12">
										<h6>Invoice Details</h6>
										<div class="invoice-table">
											<div class="table-responsive">
												<table class="table table-bordered">
													<thead>
														<tr>
															<th>Description</th>
															<th>Quatity</th>
															<th>VAT</th>
															<th>Total</th>
														</tr>
													</thead>
													<tbody>
														<tr>
															<td class="text-gray-9">General Consultation</td>
															<td>1</td>
															<td>$0</td>
															<td>$150</td>
														</tr>
														<tr>
															<td class="text-gray-9">Video Call</td>
															<td>1</td>
															<td>$0</td>
															<td>$100</td>
														</tr>
													</tbody>
												</table>
											</div>
										</div>
									</div>
									<div class="col-md-6 col-xl-4 ms-auto">
										<div class="table-responsive">
											<table class="invoice-table-two table">
												<tbody>
												<tr>
													<th>Subtotal:</th>
													<td><span>$350</span></td>
												</tr>
												<tr>
													<th>Discount:</th>
													<td><span>-10%</span></td>
												</tr>
												<tr>
													<th>Total Amount:</th>
													<td><span>$315</span></td>
												</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<!-- Invoice Information -->
							<div class="other-info mb-0">
								<h6 class="mb-2">Other information</h6>
								<p class="text-gray-9 mb-0">An account of the present illness, which includes the circumstances surrounding the onset of recent health changes and the chronology of subsequent events that have led the patient to seek medicine</p>
							</div>
							<!-- /Invoice Information -->

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /View Invoice -->

		 <!--View Report -->
		 <div class="modal fade custom-modals" id="view_report">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">View Report</h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="prescribe-download gap-2">
							<h5>21 Mar  2024</h5>
							<ul>
								<li><a href="javascript:void(0);" class="print-link"><i class="fa-solid fa-print"></i></a></li>
								<li><a href="#" class="btn btn-md btn-primary-gradient rounded-pill">Download</a></li>
							</ul>
						</div>
						<div class="view-prescribe-details p-0 border-0">

							<!-- Invoice Item -->
							<div class="invoice-item">
								<div class="row">
									<div class="col-md-6">
										<div class="invoice-info d-flex align-items-center">
											<div class="clinic-image d-inline-flex align-items-center justify-content-center">
												<img src="{{ asset('backend') }}/assets/img/icons/vtaplus.svg" alt="img">
											</div>
											<div>
												<h6 class="fs-16 fw-semibold">Vitalplus Clinic</h6>
												<p class="fs-14 fw-medium">Dr. Sandy Maria</p>
												<p class="fs-14">MBLS,MS</p>
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="invoice-info2">
											<p><span>Test Type : </span>CBC</p>
											<p><span>Collected On : </span>20 Mar 2024, 10:00 AM</p>
											<p><span>Reported On :  </span>21 Mar 2024, 11:00 AM</p>
										</div>
									</div>
									<div class="col-md-12">
										<div class="patient-infos d-flex align-items-center justify-content-between gap-3 flex-wrap">
											<div class="d-flex align-items-center">
												<span class="avatar me-2">
													<img src="{{ asset('backend') }}/assets/img/doctors-dashboard/profile-06.jpg" class="rounded" alt="img">
												</span>
												<div>
													<h6 class="fs-14 fw-medium">Hendrita Kearns</h6>
													<p>Patient ID : PT254654</p>
												</div>
											</div>
											<div>
												<h6 class="fs-14 fw-medium">Gender</h6>
												<p>Female</p>
											</div>
											<div>
												<h6 class="fs-14 fw-medium">Age</h6>
												<p>32 years </p>
											</div>
											<div>
												<h6 class="fs-14 fw-medium">Blood</h6>
												<p>O+</p>
											</div>
											<div>
												<h6 class="fs-14 fw-medium">Type</h6>
												<p>Outpatient</p>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
								<h6>Complete Blood Count(CBC)</h6>
								<p class="fs-14 mb-0"><span class="text-gray-9">Primary Test Type :</span> Blood</p>
							</div>

							<!-- Invoice Item -->
							<div class="invoice-item invoice-table-wrap">
								<div class="row">
									<div class="col-md-12">
										<div class="table-responsive inv-table">
											<table class="invoice-table table table-bordered">
												<thead>
													<tr>
														<th>Investigation</th>
														<th>Result</th>
														<th>Reference Value</th>
														<th>Unit</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td class="report-title" colspan="4">HEMOGLOBIN</td>
													</tr>
													<tr>
														<td>Hemoglobin (Hb)</td>
														<td>12.5<span class="badge badge-info-transparent text-xs d-inline-block rounded-pill ms-1">Low</span></td>
														<td>13.0 - 17.0</td>
														<td>g/dL</td>
													</tr>
													<tr>
														<td class="report-title" colspan="4">RBC COUNT</td>
													</tr>
													<tr>
														<td>Total RBC Count</td>
														<td>5.2</td>
														<td>4.5 - 5.5</td>
														<td>million cells/µL</td>
													</tr>
													<tr>
														<td class="report-title" colspan="4">BLOOD INDICES</td>
													</tr>
													<tr>
														<td>Packed Cell Volume (PCV)</td>
														<td class="text-danger">57.5<span class="badge badge-danger-transparent text-xs d-inline-block rounded-pill ms-1">High</span></td>
														<td>40 - 50</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Mean Corpuscular Volume (MCV) <span class="fs-10 text-gray-6">Calculated</span></td>
														<td>87.75</td>
														<td>83 - 101</td>
														<td>fL</td>
													</tr>
													<tr>
														<td>MCH Calculated</td>
														<td>27.72</td>
														<td>27 - 32</td>
														<td>pg</td>
													</tr>
													<tr>
														<td>MCHC Calculated</td>
														<td>32.8</td>
														<td>32.5 - 34.5</td>
														<td>g/dL</td>
													</tr>
													<tr>
														<td>RDW</td>
														<td>13.6</td>
														<td>11.6 - 14.0</td>
														<td>%</td>
													</tr>
													<tr>
														<td class="report-title" colspan="4">WBC COUNT</td>
													</tr>
													<tr>
														<td>Total WBC Count</td>
														<td>9000</td>
														<td>4000 - 11000</td>
														<td>cells/µL</td>
													</tr>
													<tr>
														<td class="report-title" colspan="4">DIFFERENTIAL WBC COUNT</td>
													</tr>
													<tr>
														<td>Neutrophils</td>
														<td>60</td>
														<td>50 - 62</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Lymphocytes</td>
														<td>31</td>
														<td>20 - 40</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Eosinophils</td>
														<td>01</td>
														<td>00 - 06</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Monocytes</td>
														<td>07</td>
														<td>00 - 10</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Basophils</td>
														<td>01</td>
														<td>00 - 02</td>
														<td>%</td>
													</tr>
													<tr>
														<td>Platelet Count</td>
														<td>248157</td>
														<td>150000 - 410000</td>
														<td>µL</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<p class="mb-2"><span class="text-gray-9 fw-medium">Instruments :</span> Fully Automated Cell Counter - Mindray 300</p>
							<p class="mb-3"><span class="text-gray-9 fw-medium">Interpretation :</span> Further confirm for Anemia</p>

							<div class="row align-items-center">
								<div class="col-md-6">
									<div class="scan-wrap">
										<h6>Scan to download report</h6>
										<img src="{{ asset('backend') }}/assets/img/scan.png" alt="scan">
									</div>
								</div>
								<div class="col-md-6">
									<div class="prescriber-info">
										<h6>Dr. Edalin Hendry</h6>
										<p>Dept of Cardiology</p>
									</div>
								</div>
							</div>

							<ul class="nav inv-paginate justify-content-center">
								<li>Page 01 of <a href="#" data-bs-toggle="modal" data-bs-target="#view_prescription2" data-bs-dismiss="modal">02</a></li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /View Report -->

		<!--View Prescription -->
		<div class="modal fade custom-modals" id="view_prescription">
			<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">View Prescription</h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<div class="modal-body pb-0">
						<div class="prescribe-download">
							<h5>21 Mar  2024</h5>
							<ul>
								<li><a href="javascript:void(0);" class="print-link"><i class="isax isax-printer"></i></a></li>
								<li><a href="#" class="btn btn-primary-gradient rounded-pill">Download</a></li>
							</ul>
						</div>
						<div class="view-prescribe invoice-content mb-0">
							<div class="invoice-item">
								<div class="row">
									<div class="col-md-6">
										<div class="invoice-logo">
											<img src="{{ asset('backend') }}/assets/img/logo.svg" alt="logo">
										</div>
									</div>
									<div class="col-md-6">
										<p class="invoice-details">
											<strong>Prescription ID :</strong> #PR-123 <br>
											<strong>Issued:</strong> 21 Mar 2024
										</p>
									</div>
								</div>
							</div>

							<!-- Invoice Item -->
							<div class="invoice-item">
								<div class="row">
									<div class="col-md-6">
										<div class="invoice-info">
											<h6 class="customer-text">Doctor Details</h6>
											<p class="invoice-details invoice-details-two">
												Edalin Hendry <br>
												806 Twin Willow Lane, <br>
												Newyork, USA <br>
											</p>
										</div>
									</div>
									<div class="col-md-6">
										<div class="invoice-info invoice-info2">
											<h6 class="customer-text">Patient Details</h6>
											<p class="invoice-details">
												Adrian Marshall <br>
												299 Star Trek Drive,<br>
												Florida, 32405, USA <br>
											</p>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<!-- Invoice Item -->
							<div class="invoice-item invoice-table-wrap">
								<div class="row">
									<div class="col-md-12">
										<h6>Prescription  Details</h6>
										<div class="table-responsive">
											<table class="invoice-table table table-bordered">
												<thead>
													<tr>
														<th>Medicine Name</th>
														<th>Dosage</th>
														<th>Frequency</th>
														<th>Duration</th>
														<th>Timings</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Ecosprin 75MG [Asprin 75 MG Oral Tab]</td>
														<td>75 mg <span>Oral Tab</span></td>
														<td>1-0-0-1</td>
														<td>1 month</td>
														<td>Before Meal</td>
													</tr>
													<tr>
														<td>Alexer 90MG Tab</td>
														<td>90 mg <span>Oral Tab</span></td>
														<td>1-0-0-1</td>
														<td>1 month</td>
														<td>Before Meal</td>
													</tr>
													<tr>
														<td>Ramistar XL2.5</td>
														<td>60 mg <span>Oral Tab</span></td>
														<td>1-0-0-0</td>
														<td>1 month</td>
														<td>After Meal</td>
													</tr>
													<tr>
														<td>Metscore</td>
														<td>90 mg <span>Oral Tab</span></td>
														<td>1-0-0-1</td>
														<td>1 month</td>
														<td>After Meal</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</div>
							</div>
							<!-- /Invoice Item -->

							<!-- Invoice Information -->
							<div class="other-info">
								<h4>Other information</h4>
								<p class="mb-0">An account of the present illness, which includes the circumstances surrounding the onset of recent health changes and the chronology of subsequent events that have led the patient to seek medicine</p>
							</div>
							<div class="other-info">
								<h4>Follow Up</h4>
								<p class="mb-0">Follow up after 3 months, Have to come on empty stomach</p>
							</div>
							<div class="prescriber-info">
								<h6>Dr. Edalin Hendry</h6>
								<p>Dept of Cardiology</p>
							</div>
							<!-- /Invoice Information -->

						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /View Prescription -->

		<!-- Delete -->
		<div class="modal fade custom-modals" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body p-4 text-center">
						<form action="patient-dashboard.html">
							<span class="del-icon mb-2 mx-auto">
								<i class="isax isax-trash"></i>
							</span>
							<h3 class="mb-2">Delete Record</h3>
							<p class="mb-3">Are you sure you want to delete this record?</p>
							<div class="d-flex justify-content-center flex-wrap gap-3">
								<a href="#" class="btn btn-md btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Yes Delete</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
		<!-- /Delete -->

		<!-- jQuery -->
		<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

		<!-- Bootstrap Core JS -->
		<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Sticky Sidebar JS -->
        <script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}x"></script>
        <script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

		<!-- select JS -->
		<script src="{{ asset('backend/assets/plugins/select2/js/select2.min.js') }}"></script>

		<!-- Owl Carousel JS -->
		<script src="{{ asset('backend/assets/js/owl.carousel.min.js') }}"></script>

		<!-- Apexchart JS -->
		<script src="{{ asset('backend/assets/plugins/apex/apexcharts.min.js') }}"></script>
		<script src="{{ asset('backend/assets/plugins/apex/chart-data.js') }}"></script>

		<!-- Datepicker JS -->
		<script src="{{ asset('backend/assets/js/moment.min.js') }}"></script>
		<script src="{{ asset('backend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

		<!-- Profile Settings JS -->
		<script src="{{ asset('backend/assets/js/profile-settings.js') }}"></script>

		<!-- Circle Progress JS -->
		<script src="{{ asset('backend/assets/js/circle-progress.min.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	</body>
</html>