@if ($homeServices->isNotEmpty())
<section class="services-section aos" data-aos="fade-up">
				<div class="horizontal-slide d-flex" data-direction="right" data-speed="slow">
					<div class="slide-list d-flex gap-4">
						@foreach ($homeServices as $homeService)
							<div class="services-slide">
								<h6><a href="{{ $homeService->url ?: 'javascript:void(0);' }}">{{ $homeService->title }}</a></h6>
							</div>
						@endforeach
					</div>
				</div>
			</section>
@endif
