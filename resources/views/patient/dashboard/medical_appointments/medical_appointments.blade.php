@extends('patient.patient_master')
@section('patient')

						<div class="col-lg-8 col-xl-9">

							<div class="dashboard-header flex-wrap">
								<h3>Records</h3>
								<div class="appointment-tabs">
									<ul class="nav">
										<li>
											<a href="#" class="nav-link active" data-bs-toggle="tab" data-bs-target="#medical">Medical Records</a>
										</li>
										<li>
											<a href="#" class="nav-link" data-bs-toggle="tab" data-bs-target="#prescription">Prescriptions</a>
										</li>
									</ul>
								</div>
							</div>

							<div class="tab-content pt-0">

								<!-- Prescription Tab -->
								<div class="tab-pane fade" id="prescription">
									<div class="dashboard-header border-0 m-0">
										<ul class="header-list-btns">
											<li>
												<div class="input-block dash-search-input">
													<input type="text" class="form-control" placeholder="Search">
													<span class="search-icon"><i class="isax isax-search-normal"></i></span>
												</div>
											</li>
										</ul>
									</div>

									@if ($prescriptions->isEmpty())
										<div class="custom-table">
											<p class="text-center mb-0 py-4">You don't have any prescriptions yet.</p>
										</div>
									@else
										<div class="custom-table">
											<div class="table-responsive">
												<table class="table table-center mb-0">
													<thead>
														<tr>
															<th>ID</th>
															<th>Created Date</th>
															<th>Prescriped By</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($prescriptions as $prescription)
															<tr>
																<td><a class="link-primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_prescription_{{ $prescription->id }}">#{{ $prescription->prescription_number }}</a></td>
																<td>{{ $prescription->issued_at->format('d M Y, h:i A') }}</td>
																<td>
																	<h2 class="table-avatar">
																		<span class="avatar avatar-sm me-2">
																			<img class="avatar-img rounded-3" src="{{ $prescription->doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-02.jpg') }}" alt="User Image">
																		</span>
																		{{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }}
																	</h2>
																</td>
																<td>
																	<div class="action-item">
																		<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_prescription_{{ $prescription->id }}">
																			<i class="isax isax-link-2"></i>
																		</a>
																	</div>
																</td>
															</tr>
														@endforeach
													</tbody>
												</table>
											</div>
										</div>
									@endif

								</div>
								<!-- /Prescription Tab -->

								<!-- Medical Records Tab -->
								<div class="tab-pane fade show active" id="medical">
									<div class="dashboard-header border-0 m-0">
										<ul class="header-list-btns">
											<li>
												<div class="input-block dash-search-input">
													<input type="text" class="form-control" placeholder="Search">
													<span class="search-icon"><i class="isax isax-search-normal"></i></span>
												</div>
											</li>
										</ul>
										<a href="javascript:void(0);" class="btn btn-md btn-primary-gradient rounded-pill" data-bs-toggle="modal" data-bs-target="#add_medical_records">Add Medical Record</a>
									</div>

									@if ($medicalRecords->isEmpty())
										<div class="custom-table">
											<p class="text-center mb-0 py-4">You haven't added any medical records yet.</p>
										</div>
									@else
										<div class="custom-table">
											<div class="table-responsive">
												<table class="table table-center mb-0">
													<thead>
														<tr>
															<th>ID</th>
															<th>Name</th>
															<th>Date</th>
															<th>Record For</th>
															<th>Comments</th>
															<th>Action</th>
														</tr>
													</thead>
													<tbody>
														@foreach ($medicalRecords as $record)
															<tr>
																<td><a class="link-primary" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_report_{{ $record->id }}">#MR{{ str_pad($record->id, 4, '0', STR_PAD_LEFT) }}</a></td>
																<td>
																	<a href="javascript:void(0);" class="lab-icon">{{ $record->title }}</a>
																</td>
																<td>{{ $record->record_date->format('d M Y') }}</td>
																<td>{{ $record->record_for }}</td>
																<td>{{ Str::limit($record->comments, 40) }}</td>
																<td>
																	<div class="action-item">
																		<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#view_report_{{ $record->id }}">
																			<i class="isax isax-link-2"></i>
																		</a>
																		<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#edit_medical_records_{{ $record->id }}">
																			<i class="isax isax-edit-2"></i>
																		</a>
																		@if ($record->file_path)
																			<a href="{{ $record->file_url }}" target="_blank" rel="noopener">
																				<i class="isax isax-import"></i>
																			</a>
																		@endif
																		<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal_{{ $record->id }}">
																			<i class="isax isax-trash"></i>
																		</a>
																	</div>
																</td>
															</tr>
														@endforeach
													</tbody>
												</table>
											</div>
										</div>
									@endif

								</div>
								<!-- /Medical Records Tab -->


							</div>

						</div>

		<!-- Add Medical Records Modal -->
		<div class="modal fade custom-modals" id="add_medical_records">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h3 class="modal-title">Add Medical Record</h3>
						<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
							<i class="fa-solid fa-xmark"></i>
						</button>
					</div>
					<form action="{{ route('patient.medical_records.store') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<input type="hidden" name="form_context" value="add_medical_records">
						<div class="modal-body pb-0">
							<div class="row">
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Title <span class="text-danger">*</span></label>
										<input type="text" name="title" class="form-control" value="{{ old('title') }}">
										@error('title')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-6">
									<div class="mb-3">
										<label class="col-form-label">Record  For <span class="text-danger">*</span></label>
										<input type="text" name="record_for" class="form-control" value="{{ old('record_for') }}">
										@error('record_for')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Date <span class="text-danger">*</span></label>
										<div class="form-icon">
											<input type="text" name="record_date" class="form-control datetimepicker" autocomplete="off" value="{{ old('record_date') }}">
											<span class="icon"><i class="isax isax-calendar-1"></i></span>
										</div>
										@error('record_date')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Comments <span class="text-danger">*</span></label>
										<textarea name="comments" class="form-control" rows="3">{{ old('comments') }}</textarea>
										@error('comments')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-12">
									<div class="mb-3">
										<label class="col-form-label">Record <span class="text-danger">*</span></label>
										<div>
											<div class="file-upload">
												<input type="file" name="file">
												<p><i class="isax isax-document-upload me-1"></i>Upload File</p>
											</div>
										</div>
										@error('file')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<div class="modal-btn text-end">
								<a href="javascript:void(0);" class="btn btn-md btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Add Medical Records</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Add Medical Records Modal -->

		@foreach ($medicalRecords as $record)
			<!-- Edit Medical Records Modal -->
			<div class="modal fade custom-modals" id="edit_medical_records_{{ $record->id }}">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">Edit Medical Record</h3>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
								<i class="fa-solid fa-xmark"></i>
							</button>
						</div>
						<form action="{{ route('patient.medical_records.update', $record) }}" method="POST" enctype="multipart/form-data">
							@csrf
							@method('PUT')
							<input type="hidden" name="form_context" value="edit_medical_records_{{ $record->id }}">
							<div class="modal-body pb-0">
								<div class="row">
									<div class="mb-3">
										<label class="col-form-label">Title</label>
										<input type="text" name="title" class="form-control" value="{{ old('title', $record->title) }}">
										@error('title')
											<div class="text-danger">{{ $message }}</div>
										@enderror
									</div>
									<div class="col-md-6">
										<div class="mb-3">
											<label class="col-form-label">Record  For <span class="text-danger">*</span></label>
											<input type="text" name="record_for" class="form-control" value="{{ old('record_for', $record->record_for) }}">
											@error('record_for')
												<div class="text-danger">{{ $message }}</div>
											@enderror
										</div>
									</div>
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Date <span class="text-danger">*</span></label>
											<div class="form-icon">
												<input type="text" name="record_date" class="form-control datetimepicker" autocomplete="off" value="{{ old('record_date', $record->record_date->format('d/m/Y')) }}">
												<span class="icon"><i class="isax isax-calendar-1"></i></span>
											</div>
											@error('record_date')
												<div class="text-danger">{{ $message }}</div>
											@enderror
										</div>
									</div>
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Comments <span class="text-danger">*</span></label>
											<textarea name="comments" class="form-control" rows="3">{{ old('comments', $record->comments) }}</textarea>
											@error('comments')
												<div class="text-danger">{{ $message }}</div>
											@enderror
										</div>
									</div>
									<div class="col-md-12">
										<div class="mb-3">
											<label class="col-form-label">Record</label>
											<div>
												<div class="file-upload">
													<input type="file" name="file">
													<p><i class="isax isax-document-upload me-1"></i>Upload File</p>
												</div>
											</div>
											@if ($record->original_name)
												<p class="mb-0 mt-1 fs-14">Current file: {{ $record->original_name }}</p>
											@endif
											@error('file')
												<div class="text-danger">{{ $message }}</div>
											@enderror
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<div class="modal-btn text-end">
									<a href="javascript:void(0);" class="btn btn-md btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</a>
									<button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Save Medical Records</button>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
			<!-- /Edit Medical Records Modal -->

			<!--View Report -->
			<div class="modal fade custom-modals" id="view_report_{{ $record->id }}">
				<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<h3 class="modal-title">{{ $record->title }}</h3>
							<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
								<i class="fa-solid fa-xmark"></i>
							</button>
						</div>
						<div class="modal-body pb-0">
							<div class="prescribe-download gap-2">
								<h5>{{ $record->record_date->format('d M Y') }}</h5>
								@if ($record->file_path)
									<ul>
										<li><a href="{{ $record->file_url }}" target="_blank" rel="noopener" class="btn btn-md btn-primary-gradient rounded-pill">Download</a></li>
									</ul>
								@endif
							</div>
							<div class="view-prescribe-details p-0 border-0">
								<div class="row mb-3">
									<div class="col-md-6">
										<h6 class="fs-14 fw-medium">Record For</h6>
										<p>{{ $record->record_for }}</p>
									</div>
									<div class="col-md-6">
										<h6 class="fs-14 fw-medium">Date</h6>
										<p>{{ $record->record_date->format('d M Y') }}</p>
									</div>
									<div class="col-md-12">
										<h6 class="fs-14 fw-medium">Comments</h6>
										<p class="mb-0">{{ $record->comments }}</p>
									</div>
								</div>
								@if ($record->file_path)
									<p class="mb-3"><span class="text-gray-9 fw-medium">Attached file:</span> {{ $record->original_name }}</p>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /View Report -->

			<!-- Delete Medical Record -->
			<div class="modal fade custom-modals" id="delete_modal_{{ $record->id }}">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-body p-4 text-center">
							<form action="{{ route('patient.medical_records.destroy', $record) }}" method="POST">
								@csrf
								@method('DELETE')
								<span class="del-icon mb-2 mx-auto">
									<i class="isax isax-trash"></i>
								</span>
								<h3 class="mb-2">Delete Record</h3>
								<p class="mb-3">Are you sure you want to delete this record?</p>
								<div class="d-flex justify-content-center flex-wrap gap-3">
									<a href="javascript:void(0);" class="btn btn-md btn-dark rounded-pill" data-bs-dismiss="modal">Cancel</a>
									<button type="submit" class="btn btn-md btn-primary-gradient rounded-pill">Yes Delete</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
			<!-- /Delete Medical Record -->
		@endforeach

		@foreach ($prescriptions as $prescription)
			<!--View Prescription -->
			<div class="modal fade custom-modals" id="view_prescription_{{ $prescription->id }}">
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
								<h5>{{ $prescription->issued_at->format('d M Y') }}</h5>
							</div>
							<div class="view-prescribe invoice-content mb-0">
								<div class="invoice-item">
									<div class="row">
										<div class="col-md-6">
											<p class="invoice-details">
												<strong>Prescription ID :</strong> #{{ $prescription->prescription_number }} <br>
												<strong>Issued:</strong> {{ $prescription->issued_at->format('d M Y') }}
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
													{{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }} <br>
													{{ $prescription->doctor->email }}
												</p>
											</div>
										</div>
										<div class="col-md-6">
											<div class="invoice-info invoice-info2">
												<h6 class="customer-text">Patient Details</h6>
												<p class="invoice-details">
													{{ auth()->user()->display_name ?: trim(auth()->user()->first_name.' '.auth()->user()->last_name) }} <br>
													{{ auth()->user()->email }}
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
														@foreach ($prescription->items as $item)
															<tr>
																<td>{{ $item->medicine_name }}</td>
																<td>{{ $item->dosage }}</td>
																<td>{{ $item->frequency }}</td>
																<td>{{ $item->duration }}</td>
																<td>{{ $item->timings }}</td>
															</tr>
														@endforeach
													</tbody>
												</table>
											</div>
										</div>
									</div>
								</div>
								<!-- /Invoice Item -->

								@if ($prescription->other_information)
									<div class="other-info">
										<h4>Other information</h4>
										<p class="mb-0">{{ $prescription->other_information }}</p>
									</div>
								@endif
								@if ($prescription->follow_up)
									<div class="other-info">
										<h4>Follow Up</h4>
										<p class="mb-0">{{ $prescription->follow_up }}</p>
									</div>
								@endif
								<div class="prescriber-info">
									<h6>{{ 'Dr '.($prescription->doctor->display_name ?: trim($prescription->doctor->first_name.' '.$prescription->doctor->last_name)) }}</h6>
									<p>{{ $prescription->doctor->designation }}</p>
								</div>

							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /View Prescription -->
		@endforeach

		<!-- Delete -->
		<div class="modal fade custom-modals" id="delete_modal">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-body p-4 text-center">
						<form action="medical-records.html">
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

		@if ($errors->any() && old('form_context'))
			<script>
				document.addEventListener('DOMContentLoaded', function () {
					var modalEl = document.getElementById(@json(old('form_context')));
					if (modalEl && typeof bootstrap !== 'undefined') {
						bootstrap.Modal.getOrCreateInstance(modalEl).show();
					}
				});
			</script>
		@endif
@endsection
