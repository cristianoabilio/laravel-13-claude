@extends('doctor.doctor_master')
@section('doctor')

<div class="doc-review">

    <div class="dashboard-header">
        <div class="header-back">
            <h3>Reviews</h3>
        </div>
    </div>

    <!-- Review Listing -->
    <ul class="comments-list">

        <li class="over-all-review">
            <div class="review-content">
                <div class="review-rate">
                    <h6>Overall Rating</h6>
                    <div class="star-rated">
                        <span>{{ $averageRating }}</span>
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fa-solid fa-star {{ $i <= round($averageRating) ? 'filled' : '' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
        </li>
        @forelse ($reviews as $review)
            @php
                $reviewerName = $review->patient->display_name ?: trim($review->patient->first_name.' '.$review->patient->last_name);
            @endphp
            <li>
                <div class="comments">
                    <div class="comment-head">
                        <div class="patinet-information">
                            <a href="javascript:void(0);">
                                <img src="{{ $review->patient->profile_photo_url ?: asset('backend/assets/img/doctors-dashboard/profile-01.jpg') }}" alt="{{ $reviewerName }}">
                            </a>
                            <div class="patient-info">
                                <h6><a href="javascript:void(0);">{{ $reviewerName }}</a></h6>
                                <span>{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                        <div class="star-rated">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= $review->rating ? 'filled' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="review-info">
                        <p>{{ $review->comment }}</p>
                    </div>
                </div>
            </li>
        @empty
            <li>
                <p class="text-center mb-0 py-4">You don't have any reviews yet.</p>
            </li>
        @endforelse
    </ul>
    <!-- /Comment List -->

    @if ($reviews->hasPages())
        {{ $reviews->links('vendor.pagination.doccure') }}
    @endif

</div>
@endsection
