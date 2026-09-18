<section class="speciality-section">
				<div class="container">
					<div class="section-header sec-header-one text-center aos" data-aos="fade-up">
						<span class="badge badge-primary">{{ __('Top Specialties') }}</span>
						<h2>{{ __('Highlighting the Care & Support') }}</h2>
					</div>
					@if ($specialities->isEmpty())
						<p class="text-center">{{ __('No specialities are available yet. Check back soon.') }}</p>
					@else
						<div class="owl-carousel spciality-slider aos" data-aos="fade-up">
							@foreach ($specialities as $speciality)
								<div class="spaciality-item">
									<div class="spaciality-img">
										<img src="{{ asset('backend/assets/img/specialities/speciality-01.jpg') }}" alt="{{ $speciality->name }}">
										<span class="spaciality-icon">
											<img src="{{ $speciality->image_url ?: asset('backend/assets/img/specialities/speciality-01.jpg') }}" alt="{{ $speciality->name }}">
										</span>
									</div>
									<h6><a href="{{ route('doctor.all.speciality', $speciality) }}">{{ $speciality->name }}</a></h6>
									<p class="mb-0">{{ trans_choice(':count Doctor|:count Doctors', $speciality->doctors_count, ['count' => $speciality->doctors_count]) }}</p>
								</div>
							@endforeach
						</div>
					@endif
					<div class="spciality-nav nav-bottom owl-nav"></div>
				</div>
			</section>
