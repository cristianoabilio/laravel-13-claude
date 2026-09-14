@extends('admin.admin_master')
@section('admin')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Manage Services</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Home</a></li>
                    <li class="breadcrumb-item active">Services</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="#add_service" data-bs-toggle="modal" class="btn btn-primary float-end mt-2">Add</a>
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
                    @if ($homeServices->isEmpty())
                        <p class="text-center mb-0 py-4">No services have been added yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>URL</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($homeServices as $homeService)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $homeService->title }}</td>
                                            <td>{{ $homeService->url ?: '-' }}</td>
                                            <td>{{ $homeService->sort_order }}</td>
                                            <td>
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_service_{{ $homeService->id }}">
                                                        <i class="fe fe-pencil"></i> Edit
                                                    </a>
                                                    <a data-bs-toggle="modal" href="#delete_service_{{ $homeService->id }}" class="btn btn-sm bg-danger-light">
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
<div class="modal fade" id="add_service" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.home.services.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="add_service">
                    <div class="mb-3">
                        <label class="mb-2">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
                        @error('title')
                            <span class="invalid-feedback d-block">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="mb-2">Link (optional)</label>
                        <input type="text" name="url" value="{{ old('url') }}" placeholder="https://..." class="form-control @error('url') is-invalid @enderror">
                        @error('url')
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

@foreach ($homeServices as $homeService)
    <!-- Edit Modal -->
    <div class="modal fade" id="edit_service_{{ $homeService->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.home.services.update', $homeService) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit_service_{{ $homeService->id }}">
                        <div class="mb-3">
                            <label class="mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title', $homeService->title) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Link (optional)</label>
                            <input type="text" name="url" value="{{ old('url', $homeService->url) }}" placeholder="https://..." class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Order</label>
                            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $homeService->sort_order) }}" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_service_{{ $homeService->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete "{{ $homeService->title }}"?</p>
                        <form action="{{ route('admin.home.services.destroy', $homeService) }}" method="POST">
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
