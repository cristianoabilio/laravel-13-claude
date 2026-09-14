@extends('admin.admin_master')
@section('admin')
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Manage Banner</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0);">Manage Home</a></li>
                    <li class="breadcrumb-item active">Banner</li>
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
                    <form action="{{ route('admin.home.banner.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-12 col-lg-6">
                                <div class="mb-3">
                                    <label class="mb-2">Heading - Before highlighted word</label>
                                    <input type="text" name="heading_prefix" value="{{ old('heading_prefix', $banner->heading_prefix) }}" class="form-control @error('heading_prefix') is-invalid @enderror">
                                    @error('heading_prefix')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "Discover Health: Find Your Trusted"</small>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="mb-3">
                                    <label class="mb-2">Heading - Highlighted word</label>
                                    <input type="text" name="heading_highlight" value="{{ old('heading_highlight', $banner->heading_highlight) }}" class="form-control @error('heading_highlight') is-invalid @enderror">
                                    @error('heading_highlight')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "Doctors"</small>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3">
                                <div class="mb-3">
                                    <label class="mb-2">Heading - After highlighted word</label>
                                    <input type="text" name="heading_suffix" value="{{ old('heading_suffix', $banner->heading_suffix) }}" class="form-control @error('heading_suffix') is-invalid @enderror">
                                    @error('heading_suffix')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">Example: "Today"</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="mb-3">
                                    <label class="mb-2">Banner Image</label>
                                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror" accept="image/*,.svg">
                                    @error('image')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">JPG, PNG, GIF, WEBP or SVG. Raster images are resized to 464 x 606 px; SVG is stored as-is. Leave empty to keep the current image.</small>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                @if ($banner->image_url)
                                    <label class="mb-2 d-block">Current Image</label>
                                    <img src="{{ $banner->image_url }}" alt="Banner preview" style="max-width: 232px; max-height: 303px;" class="rounded border">
                                @endif
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
