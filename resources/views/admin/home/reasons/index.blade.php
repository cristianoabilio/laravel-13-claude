@extends('admin.admin_master')
@section('admin')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Manage Reasons</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Home</a></li>
                    <li class="breadcrumb-item active">Reasons</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="#add_reason" data-bs-toggle="modal" class="btn btn-primary float-end mt-2">Add</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Section heading -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Section Heading</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home.reasons.section.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="section">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label class="mb-2">Badge Text</label>
                                    <input type="text" name="badge_text" value="{{ old('badge_text', $section->badge_text) }}" class="form-control @error('badge_text') is-invalid @enderror">
                                    @error('badge_text')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "Why Book With Us"</small>
                                </div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div class="mb-3">
                                    <label class="mb-2">Heading</label>
                                    <input type="text" name="heading" value="{{ old('heading', $section->heading) }}" class="form-control @error('heading') is-invalid @enderror">
                                    @error('heading')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "Compelling Reasons to Choose"</small>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Section heading -->

    <!-- Reason items -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Reason Items</h4>
                </div>
                <div class="card-body">
                    @if ($homeReasons->isEmpty())
                        <p class="text-center mb-0 py-4">No reasons have been added yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Icon</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($homeReasons as $homeReason)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td><i class="{{ $homeReason->icon }} {{ $homeReason->icon_color }} fs-18"></i></td>
                                            <td>{{ $homeReason->title }}</td>
                                            <td>{{ Str::limit($homeReason->description, 60) }}</td>
                                            <td>{{ $homeReason->sort_order }}</td>
                                            <td>
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_reason_{{ $homeReason->id }}">
                                                        <i class="fe fe-pencil"></i> Edit
                                                    </a>
                                                    <a data-bs-toggle="modal" href="#delete_reason_{{ $homeReason->id }}" class="btn btn-sm bg-danger-light">
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
    <!-- /Reason items -->
</div>

<!-- Add Modal -->
<div class="modal fade" id="add_reason" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Reason</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.home.reasons.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="add_reason">
                    <div class="row">
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Icon Class</label>
                                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="isax isax-tag-user5" class="form-control @error('icon') is-invalid @enderror">
                                @error('icon')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="mb-3">
                                <label class="mb-2">Icon Color Class</label>
                                <input type="text" name="icon_color" value="{{ old('icon_color') }}" placeholder="text-orange" class="form-control @error('icon_color') is-invalid @enderror">
                                @error('icon_color')
                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Description</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Order</label>
                        <input type="number" min="0" name="sort_order" value="{{ old('sort_order') }}" class="form-control @error('sort_order') is-invalid @enderror">
                        @error('sort_order')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Modal -->

@foreach ($homeReasons as $homeReason)
    <!-- Edit Modal -->
    <div class="modal fade" id="edit_reason_{{ $homeReason->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Reason</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.home.reasons.update', $homeReason) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit_reason_{{ $homeReason->id }}">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Icon Class</label>
                                    <input type="text" name="icon" value="{{ old('icon', $homeReason->icon) }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label class="mb-2">Icon Color Class</label>
                                    <input type="text" name="icon_color" value="{{ old('icon_color', $homeReason->icon_color) }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title', $homeReason->title) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Description</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $homeReason->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Order</label>
                            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $homeReason->sort_order) }}" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_reason_{{ $homeReason->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete "{{ $homeReason->title }}"?</p>
                        <form action="{{ route('admin.home.reasons.destroy', $homeReason) }}" method="POST">
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

@if ($errors->any() && old('form_context') && old('form_context') !== 'section')
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
