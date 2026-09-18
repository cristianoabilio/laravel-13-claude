<section class="bookus-section bg-dark">
				<div class="container">
					<div class="row align-items-center row-gap-4">
						<div class="col-lg-6">
							<div class="bookus-img">
								<div class="row g-3">
									<div class="col-md-12 aos" data-aos="fade-up">
										<img src="{{ $homeBookUsSection->image_one_url ?: asset('backend/assets/img/book-01.jpg') }}" alt="img" class="img-fluid" width="1060" height="516">
									</div>
									<div class="col-sm-6 aos" data-aos="fade-up">
										<img src="{{ $homeBookUsSection->image_two_url ?: asset('backend/assets/img/book-02.jpg') }}" alt="img" class="img-fluid" width="512" height="516">
									</div>
									<div class="col-sm-6 aos" data-aos="fade-up">
										<img src="{{ $homeBookUsSection->image_three_url ?: asset('backend/assets/img/book-03.jpg') }}" alt="img" class="img-fluid" width="512" height="516">
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="section-header sec-header-one mb-2 aos" data-aos="fade-up">
								@if ($homeBookUsSection->badge_text)
									<span class="badge badge-primary">{{ $homeBookUsSection->badge_text }}</span>
								@endif
								<h2 class="text-white mb-3">{{ $homeBookUsSection->heading_prefix }} <span class="text-primary-gradient">{{ $homeBookUsSection->heading_highlight }}</span></h2>
							</div>
							<p class="text-light mb-4">{{ $homeBookUsSection->description }}</p>
							@if ($homeBookUsFaqs->isNotEmpty())
								<div class="faq-info aos" data-aos="fade-up">
									<div class="accordion" id="faq-details">
										@foreach ($homeBookUsFaqs as $faq)
											<!-- FAQ Item -->
											<div class="accordion-item">
												<h2 class="accordion-header" id="heading{{ $faq->id }}">
													<a href="javascript:void(0);" class="accordion-button @if (! $loop->first) collapsed @endif" data-bs-toggle="collapse" data-bs-target="#collapse{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="collapse{{ $faq->id }}">
														{{ sprintf('%02d', $loop->iteration) }} . {{ $faq->title }}
													</a>
												</h2>
												<div id="collapse{{ $faq->id }}" class="accordion-collapse collapse @if ($loop->first) show @endif" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faq-details">
													<div class="accordion-body">
														<div class="accordion-content">
															<p>{{ $faq->description }}</p>
														</div>
													</div>
												</div>
											</div>
											<!-- /FAQ Item -->
										@endforeach
									</div>
								</div>
							@endif
						</div>
					</div>
					<div class="bookus-sec">
						<div class="row g-4">
							<div class="col-lg-3">
								<div class="book-item">
									<div class="book-icon bg-primary">
										<i class="isax isax-search-normal5"></i>
									</div>
									<div class="book-info">
										<h6 class="text-white mb-2">{{ __('Search For Doctors') }}</h6>
										<p class="fs-14 text-light">{{ __('Search for a doctor based on specialization, location, or availability for your Treatements') }}</p>
									</div>
									<div class="way-icon">
										<img src="{{ asset('backend/assets/img/icons/way-icon.svg') }}" alt="img">
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="book-item">
									<div class="book-icon bg-orange">
										<i class="isax isax-security-user5"></i>
									</div>
									<div class="book-info">
										<h6 class="text-white mb-2">{{ __('Check Doctor Profile') }}</h6>
										<p class="fs-14 text-light">{{ __('Explore detailed doctor profiles on our platform to make informed healthcare decisions.') }}</p>
									</div>
									<div class="way-icon">
										<img src="{{ asset('backend/assets/img/icons/way-icon.svg') }}" alt="img">
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="book-item">
									<div class="book-icon bg-cyan">
										<i class="isax isax-calendar5"></i>
									</div>
									<div class="book-info">
										<h6 class="text-white mb-2">{{ __('Schedule Appointment') }}</h6>
										<p class="fs-14 text-light">{{ __('After choose your preferred doctor, select a convenient time slot, & confirm your appointment.') }}</p>
									</div>
									<div class="way-icon">
										<img src="{{ asset('backend/assets/img/icons/way-icon.svg') }}" alt="img">
									</div>
								</div>
							</div>
							<div class="col-lg-3">
								<div class="book-item">
									<div class="book-icon bg-indigo">
										<i class="isax isax-blend5"></i>
									</div>
									<div class="book-info">
										<h6 class="text-white mb-2">{{ __('Get Your Solution') }}</h6>
										<p class="fs-14 text-light">{{ __('Discuss your health concerns with the doctor and receive the personalized advice & with solution.') }}</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</section>
