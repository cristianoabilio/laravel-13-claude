@extends('admin.admin_master')
@section('admin')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Manage Testimonials</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Testimonials</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="#add_testimonial" data-bs-toggle="modal" class="btn btn-primary float-end mt-2">Add</a>
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
                    @if ($testimonials->isEmpty())
                        <p class="text-center mb-0 py-4">No testimonials have been added yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Image</th>
                                        <th>Title</th>
                                        <th>Quote</th>
                                        <th>Patient</th>
                                        <th>Country</th>
                                        <th>Rating</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($testimonials as $testimonial)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if ($testimonial->image_url)
                                                    <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->patient_name }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                                @endif
                                            </td>
                                            <td>{{ $testimonial->title }}</td>
                                            <td>{{ Str::limit($testimonial->quote, 50) }}</td>
                                            <td>{{ $testimonial->patient_name }}</td>
                                            <td>{{ $testimonial->patient_country }}</td>
                                            <td>{{ $testimonial->rating }} / 5</td>
                                            <td>{{ $testimonial->sort_order }}</td>
                                            <td>
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_testimonial_{{ $testimonial->id }}">
                                                        <i class="fe fe-pencil"></i> Edit
                                                    </a>
                                                    <a data-bs-toggle="modal" href="#delete_testimonial_{{ $testimonial->id }}" class="btn btn-sm bg-danger-light">
                                                        <i class="fe fe-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="add_testimonial" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Testimonial</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.testimonials.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="form_context" value="add_testimonial">
                    <div class="mb-3">
                        <label class="mb-2">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Quote</label>
                        <textarea name="quote" rows="3" class="form-control @error('quote') is-invalid @enderror">{{ old('quote') }}</textarea>
                        @error('quote')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Patient Name</label>
                                <input type="text" name="patient_name" value="{{ old('patient_name') }}" class="form-control @error('patient_name') is-invalid @enderror">
                                @error('patient_name')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Patient Country</label>
                                <input type="text" name="patient_country" value="{{ old('patient_country') }}" class="form-control @error('patient_country') is-invalid @enderror">
                                @error('patient_country')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Rating</label>
                                <input type="number" min="1" max="5" name="rating" value="{{ old('rating', 5) }}" class="form-control @error('rating') is-invalid @enderror">
                                @error('rating')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Order</label>
                                <input type="number" min="0" name="sort_order" value="{{ old('sort_order') }}" class="form-control @error('sort_order') is-invalid @enderror">
                                @error('sort_order')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Patient Image</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*,.svg">
                        @error('image')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                        <small class="form-text text-muted">Resized to 300 x 300 px.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Modal -->

@foreach ($testimonials as $testimonial)
    <!-- Edit Modal -->
    <div class="modal fade" id="edit_testimonial_{{ $testimonial->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Testimonial</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit_testimonial_{{ $testimonial->id }}">
                        <div class="mb-3">
                            <label class="mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title', $testimonial->title) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Quote</label>
                            <textarea name="quote" rows="3" class="form-control">{{ old('quote', $testimonial->quote) }}</textarea>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Patient Name</label>
                                    <input type="text" name="patient_name" value="{{ old('patient_name', $testimonial->patient_name) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Patient Country</label>
                                    <input type="text" name="patient_country" value="{{ old('patient_country', $testimonial->patient_country) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Rating</label>
                                    <input type="number" min="1" max="5" name="rating" value="{{ old('rating', $testimonial->rating) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Order</label>
                                    <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $testimonial->sort_order) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Patient Image</label>
                            <input type="file" name="image" class="form-control" accept="image/*,.svg">
                            <small class="form-text text-muted">Resized to 300 x 300 px. Leave empty to keep the current image.</small>
                            @if ($testimonial->image_url)
                                <img src="{{ $testimonial->image_url }}" alt="{{ $testimonial->patient_name }}" class="rounded-circle mt-2 d-block" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_testimonial_{{ $testimonial->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete "{{ $testimonial->title }}" from {{ $testimonial->patient_name }}?</p>
                        <form action="{{ route('admin.testimonials.destroy', $testimonial) }}" method="POST">
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

@if ($errors->any() && old('form_context'))
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById(@json(old('form_context')));
            if (modalEl && typeof bootstrap !== 'undefined') {
                bootstrap.Modal.getOrCreateInstance(modalEl).show();
            }
        });
    </script>
@endif
@endsection
