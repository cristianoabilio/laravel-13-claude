<section class="testimonial-section-one">
				<div class="container">
					<div class="section-header sec-header-one text-center aos" data-aos="fade-up">
						<span class="badge badge-primary">Testimonials</span>
						<h2>15k Users Trust Doccure Worldwide</h2>
					</div>

					<!-- Testimonial Slider -->
					@if ($testimonials->isNotEmpty())
						<div class="owl-carousel testimonials-slider aos" data-aos="fade-up">
							@foreach ($testimonials as $testimonial)
								<div class="card shadow-none mb-0">
									<div class="card-body">
										<div class="d-flex align-items-center mb-4">
											<div class="rating d-flex">
												@for ($i = 1; $i <= 5; $i++)
													<i class="fa-solid fa-star @if ($i <= $testimonial->rating) filled @endif @if ($i < 5) me-1 @endif"></i>
												@endfor
											</div>
											<span>
												<img src="{{ asset('backend/assets/img/icons/quote-icon.svg') }}" alt="img">
											</span>
										</div>
										<h6 class="fs-16 fw-medium mb-2">{{ $testimonial->title }}</h6>
										<p>{{ $testimonial->quote }}</p>
										<div class="d-flex align-items-center">
											<a href="javascript:void(0);" class="avatar avatar-lg">
												<img src="{{ $testimonial->image_url ?: asset('backend/assets/img/patients/patient.jpg') }}" class="rounded-circle" alt="img" width="300" height="300">
											</a>
											<div class="ms-2">
												<h6 class="mb-1"><a href="javascript:void(0);">{{ $testimonial->patient_name }}</a></h6>
												<p class="fs-14 mb-0">{{ $testimonial->patient_country }}</p>
											</div>
										</div>
									</div>
								</div>
							@endforeach
						</div>
					@endif
					<!-- /Testimonial Slider -->

					<!-- Counter -->
					<div class="testimonial-counter">
						<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 row-gap-4">
							<div class="counter-item text-center aos" data-aos="fade-up">
								<h6 class="display-6"><span class="count-digit">500</span>+</h6>
								<p>Doctors Available</p>
							</div>
							<div class="counter-item text-center aos" data-aos="fade-up"">
								<h6 class="display-6 secondary-count"><span class="count-digit">18</span>+</h6>
								<p>Specialities</p>
							</div>
							<div class="counter-item text-center aos" data-aos="fade-up">
								<h6 class="display-6 purple-count"><span class="count-digit">30</span>K</h6>
								<p>Bookings Done</p>
							</div>
							<div class="counter-item text-center aos" data-aos="fade-up">
								<h6 class="display-6 pink-count"><span class="count-digit">97</span>+</h6>
								<p>Hospitals & Clinic</p>
							</div>
							<div class="counter-item text-center  aos" data-aos="fade-up">
								<h6 class="display-6 warning-count"><span class="count-digit">317</span>+</h6>
								<p>Lab Tests Available</p>
							</div>
						</div>
					</div>
					<!-- /Counter -->

				</div>
			</section>
