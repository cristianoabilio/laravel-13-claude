@extends('admin.admin_master')
@section('admin')

<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-7 col-auto">
                <h3 class="page-title">Manage Book Us</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Home</a></li>
                    <li class="breadcrumb-item active">Book Us</li>
                </ul>
            </div>
            <div class="col-sm-5 col">
                <a href="#add_faq" data-bs-toggle="modal" class="btn btn-primary float-end mt-2">Add FAQ</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Section -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Section</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.home.bookus.section.update') }}" method="POST" enctype="multipart/form-data">
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
                                    <label class="mb-2">Heading - Before highlighted text</label>
                                    <input type="text" name="heading_prefix" value="{{ old('heading_prefix', $section->heading_prefix) }}" class="form-control @error('heading_prefix') is-invalid @enderror">
                                    @error('heading_prefix')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "We are committed to understanding your"</small>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="mb-2">Heading - Highlighted text</label>
                                    <input type="text" name="heading_highlight" value="{{ old('heading_highlight', $section->heading_highlight) }}" class="form-control @error('heading_highlight') is-invalid @enderror">
                                    @error('heading_highlight')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "unique needs and delivering care."</small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $section->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label class="mb-2">Main Image</label>
                                    <input type="file" name="image_one" class="form-control @error('image_one') is-invalid @enderror" accept="image/*,.svg">
                                    @error('image_one')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Resized to 1060 x 516 px. Leave empty to keep the current image.</small>
                                    @if ($section->image_one_url)
                                        <img src="{{ $section->image_one_url }}" alt="Main image preview" style="max-width: 200px;" class="rounded border mt-2 d-block">
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label class="mb-2">Secondary Image 1</label>
                                    <input type="file" name="image_two" class="form-control @error('image_two') is-invalid @enderror" accept="image/*,.svg">
                                    @error('image_two')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Resized to 512 x 516 px. Leave empty to keep the current image.</small>
                                    @if ($section->image_two_url)
                                        <img src="{{ $section->image_two_url }}" alt="Secondary image 1 preview" style="max-width: 200px;" class="rounded border mt-2 d-block">
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-4">
                                <div class="mb-3">
                                    <label class="mb-2">Secondary Image 2</label>
                                    <input type="file" name="image_three" class="form-control @error('image_three') is-invalid @enderror" accept="image/*,.svg">
                                    @error('image_three')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Resized to 512 x 516 px. Leave empty to keep the current image.</small>
                                    @if ($section->image_three_url)
                                        <img src="{{ $section->image_three_url }}" alt="Secondary image 2 preview" style="max-width: 200px;" class="rounded border mt-2 d-block">
                                    @endif
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Section -->

    <!-- FAQ items -->
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">FAQ Items</h4>
                </div>
                <div class="card-body">
                    @if ($homeBookUsFaqs->isEmpty())
                        <p class="text-center mb-0 py-4">No FAQ items have been added yet.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover table-center mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Description</th>
                                        <th>Order</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($homeBookUsFaqs as $faq)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $faq->title }}</td>
                                            <td>{{ Str::limit($faq->description, 60) }}</td>
                                            <td>{{ $faq->sort_order }}</td>
                                            <td>
                                                <div class="actions">
                                                    <a class="btn btn-sm bg-success-light" data-bs-toggle="modal" href="#edit_faq_{{ $faq->id }}">
                                                        <i class="fe fe-pencil"></i> Edit
                                                    </a>
                                                    <a data-bs-toggle="modal" href="#delete_faq_{{ $faq->id }}" class="btn btn-sm bg-danger-light">
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
    <!-- /FAQ items -->
</div>

<!-- Add Modal -->
<div class="modal fade" id="add_faq" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add FAQ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('admin.home.bookus.faqs.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="add_faq">
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

@foreach ($homeBookUsFaqs as $faq)
    <!-- Edit Modal -->
    <div class="modal fade" id="edit_faq_{{ $faq->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit FAQ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.home.bookus.faqs.update', $faq) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="form_context" value="edit_faq_{{ $faq->id }}">
                        <div class="mb-3">
                            <label class="mb-2">Title</label>
                            <input type="text" name="title" value="{{ old('title', $faq->title) }}" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Description</label>
                            <textarea name="description" rows="3" class="form-control">{{ old('description', $faq->description) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="mb-2">Order</label>
                            <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $faq->sort_order) }}" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Edit Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="delete_faq_{{ $faq->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete</h4>
                        <p class="mb-4">Are you sure you want to delete "{{ $faq->title }}"?</p>
                        <form action="{{ route('admin.home.bookus.faqs.destroy', $faq) }}" method="POST">
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
