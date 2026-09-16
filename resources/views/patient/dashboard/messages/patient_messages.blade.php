<!DOCTYPE html>
<html lang="en">
	<head>

		<meta charset="utf-8">
		<title>Doccure</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="keywords" content="practo clone, doccure, doctor appointment, Practo clone html template, doctor booking template">
		<meta name="author" content="Practo Clone HTML Template - Doctor Booking Template">
		<meta property="og:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="og:type" content="website">
		<meta property="og:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta property="og:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta property="og:image" content="{{ asset('backend/assets/img/preview-banner.jpg') }}">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="twitter:domain" content="https://doccure.dreamstechnologies.com/html/">
		<meta property="twitter:url" content="https://doccure.dreamstechnologies.com/html/">
		<meta name="twitter:title" content="Doctors Appointment HTML Website Templates | Doccure">
		<meta name="twitter:description" content="The responsive professional Doccure template offers many features, like scheduling appointments with  top doctors, clinics, and hospitals via voice, video call & chat.">
		<meta name="twitter:image" content="{{ asset('backend/assets/img/preview-banner.jpg') }}">

		<!-- Favicon -->
		<link rel="shortcut icon" href="{{ asset('backend/assets/img/favicon.png') }}" type="image/x-icon">

		<!-- Apple Touch Icon -->
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png') }}">

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

		<!-- Swiper CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/plugins/swiper/swiper.min.css') }}">

		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('backend/assets/css/custom.css') }}">

		<style>
			.chat-image-attachment { max-width: 220px; border-radius: 8px; display: block; margin-top: 6px; }
			.chat-image-preview { position: relative; display: inline-block; margin-bottom: 8px; }
			.chat-image-preview img { max-height: 90px; border-radius: 8px; }
			.chat-image-preview .btn-close { position: absolute; top: -6px; right: -6px; background-color: #fff; border-radius: 50%; box-shadow: 0 0 3px rgba(0,0,0,.3); padding: 4px; }
			.user-list-item.active > a { background-color: rgba(0,0,0,.03); }
		</style>

	</head>
	<body class="main-chat-blk">

		<!-- Main Wrapper -->
		<div class="main-wrapper">

			<!-- Header -->
            @include('patient.body.header')
			<!-- /Header -->

			@if (session('error'))
				<div class="container mt-3">
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						{{ session('error') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
				</div>
			@endif

			<div id="chat-app"
				data-poll-url="{{ $activeContact ? route('patient.messages.poll', $activeContact) : '' }}"
				data-store-url="{{ $activeContact ? route('patient.messages.store', $activeContact) : '' }}"
				data-last-id="{{ $lastMessageId }}"
				hidden></div>

			<div class="page-wrapper chat-page-wrapper">
				<div class="container">

					<div class="content doctor-content">

						<div class="chat-sec">

							<!-- sidebar group -->
							<div class="sidebar-group left-sidebar chat_sidebar">

								<!-- Chats sidebar -->
								<div id="chats" class="left-sidebar-wrap sidebar active slimscroll">

									<div class="slimscroll-active-sidebar">

									   <!-- Left Chat Title -->
									   <div class="left-chat-title all-chats">
											<div class="setting-title-head">
												<h4>My Doctors</h4>
											</div>
									   </div>
									   <!-- /Left Chat Title -->

										@php $onlineContacts = $contacts->where('is_online', true)->take(6); @endphp
										@if ($onlineContacts->isNotEmpty())
											<!-- Top Online Contacts -->
											<div class="top-online-contacts">
												<div class="fav-title">
													<h6>Online Now</h6>
												</div>
												<div class="swiper-container">
													<div class="swiper-wrapper">
														@foreach ($onlineContacts as $onlineContact)
															<div class="swiper-slide">
																<div class="top-contacts-box">
																	<div class="profile-img online">
																		<img src="{{ $onlineContact->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $onlineContact->display_name ?: trim($onlineContact->first_name.' '.$onlineContact->last_name) }}">
																	</div>
																</div>
															</div>
														@endforeach
													</div>
												</div>
											</div>
											<!-- /Top Online Contacts -->
										@endif

										<div class="sidebar-body chat-body" id="chatsidebar">

											<div class="d-flex justify-content-between align-items-center ps-0 pe-0">
												<div class="fav-title pin-chat">
													<h6>Chats</h6>
												</div>
											</div>

											<ul class="user-list">
												@forelse ($contacts as $contact)
													@php
														$contactLabel = 'Dr '.($contact->display_name ?: trim($contact->first_name.' '.$contact->last_name));
														$isActive = $activeContact && $activeContact->id === $contact->id;
													@endphp
													<li class="user-list-item {{ $isActive ? 'active' : '' }}">
														<a href="{{ route('patient.messages', $contact) }}">
															<div class="avatar {{ $contact->is_online ? 'avatar-online' : '' }}">
																<img src="{{ $contact->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $contactLabel }}">
															</div>
															<div class="users-list-body">
																<div>
																	<h5>{{ $contactLabel }}</h5>
																	<p>
																		@if ($contact->last_message?->is_image_attachment)
																			<i class="fa-solid fa-image me-1"></i>Photo
																		@elseif ($contact->last_message)
																			{{ Str::limit($contact->last_message->body, 30) }}
																		@else
																			Say hello
																		@endif
																	</p>
																</div>
																<div class="last-chat-time">
																	@if ($contact->last_message)
																		<small class="text-muted">{{ $contact->last_message->created_at->diffForHumans() }}</small>
																	@endif
																	@if ($contact->unread_count)
																		<div class="new-message-count">{{ $contact->unread_count }}</div>
																	@endif
																</div>
															</div>
														</a>
													</li>
												@empty
													<li class="p-3 text-center text-muted">
														You don't have any doctors to chat with yet. Chat unlocks once an appointment together is marked completed.
													</li>
												@endforelse
											</ul>
										</div>

									</div>

								</div>
								<!-- / Chats sidebar -->
							</div>
							<!-- /Sidebar group -->

							<!-- Chat -->
							<div class="chat chat-messages" id="middle">
								<div class="slimscroll">
									<div class="chat-inner-header">
										<div class="chat-header">
											<div class="user-details">
												<div class="d-lg-none">
													<ul class="list-inline mt-2 me-2">
														<li class="list-inline-item">
															<a class="text-muted px-0 left_sides" href="#" data-chat="open">
																<i class="fas fa-arrow-left"></i>
															</a>
														</li>
													</ul>
												</div>
												@if ($activeContact)
													<figure class="avatar {{ $activeContact->is_online ? 'avatar-online' : '' }}">
														<img src="{{ $activeContact->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="image">
													</figure>
													<div class="mt-1">
														<h5>{{ 'Dr '.($activeContact->display_name ?: trim($activeContact->first_name.' '.$activeContact->last_name)) }}</h5>
														<small class="last-seen">{{ $activeContact->is_online ? 'Online' : 'Offline' }}</small>
													</div>
												@else
													<div class="mt-1">
														<h5>Messages</h5>
													</div>
												@endif
											</div>
										</div>
									</div>
									<div class="chat-body">
										<div class="messages">
											@forelse ($messages as $message)
												<div class="chats {{ $message['is_mine'] ? 'chats-right' : '' }}">
													<div class="chat-content">
														<div class="message-content">
															@if ($message['is_image'] && $message['attachment_url'])
																<img src="{{ $message['attachment_url'] }}" class="chat-image-attachment" alt="{{ $message['attachment_original_name'] }}">
															@endif
															@if ($message['body'])
																<p class="mb-0">{{ $message['body'] }}</p>
															@endif
														</div>
														<small class="text-muted d-block mt-1">{{ $message['time'] }}</small>
													</div>
												</div>
											@empty
												<div class="text-center text-muted py-5">
													@if ($contacts->isEmpty())
														You don't have any doctors to chat with yet.<br>Chat unlocks once an appointment together is marked completed.
													@elseif ($activeContact)
														No messages yet. Say hello to {{ 'Dr '.($activeContact->display_name ?: trim($activeContact->first_name.' '.$activeContact->last_name)) }}!
													@else
														Select a doctor on the left to start chatting.
													@endif
												</div>
											@endforelse
										</div>
									</div>
									@if ($activeContact)
										<div class="chat-footer">
											<form id="chat-form" enctype="multipart/form-data">
												@csrf
												<div id="chat-image-preview" class="chat-image-preview" hidden>
													<img src="" alt="preview">
													<button type="button" id="chat-image-remove" class="btn-close" aria-label="Remove image"></button>
												</div>
												<label class="smile-foot" for="chat-image-input" style="cursor:pointer;" data-bs-toggle="tooltip" title="Attach an image">
													<i class="fa-solid fa-image"></i>
												</label>
												<input type="file" id="chat-image-input" name="image" accept="image/*" hidden>
												<input type="text" name="body" class="form-control chat_form" placeholder="Type your message here..." autocomplete="off">
												<div class="form-buttons">
													<button class="btn send-btn" type="submit">
														<i class="isax isax-send-25"></i>
													</button>
												</div>
											</form>
										</div>
									@endif
								</div>
							</div>
							<!-- /Chat -->

						</div>
					</div>
				</div>
			</div>

		</div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
		<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

		<!-- Bootstrap Core JS -->
		<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

		<!-- Swiper JS -->
		<script src="{{ asset('backend/assets/plugins/swiper/swiper.min.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('backend/assets/js/script.js') }}"></script>

		<!-- Chat JS -->
		<script src="{{ asset('backend/assets/js/chat.js') }}"></script>

	</body>
</html>
