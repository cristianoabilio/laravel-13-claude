@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="page-title">Reviews</h3>
                    <ul class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Reviews</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Page Header -->

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-body">
                        @if ($reviews->isEmpty())
                            <p class="text-center mb-0 py-4">No reviews have been submitted yet.</p>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover table-center mb-0">
                                    <thead>
                                        <tr>
                                            <th>Patient Name</th>
                                            <th>Doctor Name</th>
                                            <th>Ratings</th>
                                            <th>Description</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($reviews as $review)
                                            @php
                                                $patientName = $review->patient->display_name ?: trim($review->patient->first_name.' '.$review->patient->last_name);
                                                $doctorName = $review->doctor->display_name ?: trim($review->doctor->first_name.' '.$review->doctor->last_name);
                                            @endphp
                                            <tr>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $review->patient->profile_photo_url ?: asset('backend/assets/img/patients/patient1.jpg') }}" alt="{{ $patientName }}"></span>
                                                        {{ $patientName }}
                                                    </h2>
                                                </td>
                                                <td>
                                                    <h2 class="table-avatar">
                                                        <span class="avatar avatar-sm me-2"><img class="avatar-img rounded-circle" src="{{ $review->doctor->profile_photo_url ?: asset('backend/assets/img/doctors/doctor-thumb-01.jpg') }}" alt="{{ $doctorName }}"></span>
                                                        Dr {{ $doctorName }}
                                                    </h2>
                                                </td>

                                                <td>
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="fe {{ $i <= $review->rating ? 'fe-star text-warning' : 'fe-star-o text-secondary' }}"></i>
                                                    @endfor
                                                </td>

                                                <td>
                                                    {{ Str::limit($review->comment, 60) }}
                                                </td>
                                                <td>{{ $review->created_at->format('j M Y') }} <br><small>{{ $review->created_at->format('h.i A') }}</small></td>
                                                <td>
                                                    <div class="actions">
                                                        <a class="btn btn-sm bg-danger-light" data-bs-toggle="modal" href="#delete_modal_{{ $review->id }}">
                                                            <i class="fe fe-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>

@foreach ($reviews as $review)
    <!-- Delete Modal -->
    <div class="modal fade" id="delete_modal_{{ $review->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document" >
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure want to delete this review?</p>
                        <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Delete</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Modal -->
@endforeach

@endsection
