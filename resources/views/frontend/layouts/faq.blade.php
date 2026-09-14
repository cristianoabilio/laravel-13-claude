<section class="faq-section-one">
				<div class="container">
					<div class="section-header sec-header-one text-center aos" data-aos="fade-up">
						<span class="badge badge-primary">FAQ'S</span>
						<h2>Your Questions are Answered</h2>
					</div>
					<div class="row">
						<div class="col-md-10 mx-auto">
							@if ($faqs->isNotEmpty())
								<div class="faq-info aos" data-aos="fade-up">
									<div class="accordion" id="site-faq-details">
										@foreach ($faqs as $faq)
											<!-- FAQ Item -->
											<div class="accordion-item">
												<h2 class="accordion-header" id="site-faq-heading-{{ $faq->id }}">
													<a href="javascript:void(0);" class="accordion-button @if (! $loop->first) collapsed @endif" data-bs-toggle="collapse" data-bs-target="#site-faq-collapse-{{ $faq->id }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}" aria-controls="site-faq-collapse-{{ $faq->id }}">
														{{ $faq->question }}
													</a>
												</h2>
												<div id="site-faq-collapse-{{ $faq->id }}" class="accordion-collapse collapse @if ($loop->first) show @endif" aria-labelledby="site-faq-heading-{{ $faq->id }}" data-bs-parent="#site-faq-details">
													<div class="accordion-body">
														<div class="accordion-content">
															<p>{{ $faq->answer }}</p>
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
				</div>
			</section>
