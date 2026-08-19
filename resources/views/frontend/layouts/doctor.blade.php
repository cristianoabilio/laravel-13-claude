<section class="doctor-section">
				<div class="container">
					<div class="section-header sec-header-one text-center aos" data-aos="fade-up">
						<span class="badge badge-primary">Featured Doctors</span>
						<h2>Our Highlighted Doctors</h2>
					</div>

					@if ($doctors->isEmpty())
						<p class="text-center">No doctors are available yet. Check back soon.</p>
					@else
						<div class="doctors-slider owl-carousel aos" data-aos="fade-up">
							@foreach ($doctors as $doctor)
								@php
									$clinic = $doctor->clinics->first();
									$doctorService = $doctor->doctorServices->first();
									$isAvailable = $doctor->availability_status === 'available';
								@endphp
								<div class="card">
									<div class="card-img card-img-hover">
										<a href="javascript:void(0);">
											<img src="{{ $doctor->profile_photo_url ?: asset('backend/assets/img/doctor-grid/doctor-grid-01.jpg') }}" alt="{{ $doctor->display_name ?: $doctor->first_name }}">
										</a>
										<div class="grid-overlay-item d-flex align-items-center justify-content-end">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="fa fa-heart"></i>
											</a>
										</div>
									</div>
									<div class="card-body p-0">
										<div class="d-flex active-bar align-items-center justify-content-between p-3">
											<a href="javascript:void(0)" class="text-indigo fw-medium fs-14">{{ $doctor->designation ?: 'Doctor' }}</a>
											<span class="badge {{ $isAvailable ? 'bg-success-light' : 'bg-danger-light' }} d-inline-flex align-items-center">
												<i class="fa-solid fa-circle fs-5 me-1"></i>
												{{ $isAvailable ? 'Available' : 'Not Available' }}
											</span>
										</div>
										<div class="p-3 pt-0">
											<div class="doctor-info-detail mb-3 pb-3">
												<h3 class="mb-1">
													<a href="javascript:void(0);">Dr. {{ $doctor->display_name ?: trim($doctor->first_name.' '.$doctor->last_name) }}</a>
												</h3>
												@if ($clinic?->location)
													<div class="d-flex align-items-center">
														<p class="d-flex align-items-center mb-0 fs-14"><i class="isax isax-location me-2"></i>{{ $clinic->location }}</p>
													</div>
												@endif
											</div>
											<div class="d-flex align-items-center justify-content-between">
												<div>
													<p class="mb-1">Consultation Fees</p>
													<h3 class="text-orange">{{ $doctorService ? '$'.number_format((float) $doctorService->price, 2) : 'Contact for pricing' }}</h3>
												</div>
												<a href="javascript:void(0);" class="btn btn-md btn-dark d-inline-flex align-items-center rounded-pill">
													<i class="isax isax-calendar-1 me-2"></i>
													Book Now
												</a>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
						<div class="doctor-nav nav-bottom owl-nav"></div>
					@endif
				</div>
			</section>
