<section class="reason-section">
				<div class="container">
					<div class="section-header sec-header-one text-center aos" data-aos="fade-up">
						@if ($homeReasonSection->badge_text)
							<span class="badge badge-primary">{{ $homeReasonSection->badge_text }}</span>
						@endif
						<h2>{{ $homeReasonSection->heading }}</h2>
					</div>
					@if ($homeReasons->isNotEmpty())
						<div class="row row-gap-4 justify-content-center">
							@foreach ($homeReasons as $homeReason)
								<div class="col-lg-4 col-md-6">
									<div class="reason-item aos" data-aos="fade-up">
										<h6 class="mb-2 d-flex align-items-center"><i class="{{ $homeReason->icon }} {{ $homeReason->icon_color }} me-2"></i>{{ $homeReason->title }}</h6>
										<p class="fs-14 mb-0">{{ $homeReason->description }}</p>
									</div>
								</div>
							@endforeach
						</div>
					@endif
				</div>
			</section>
