@extends('doctor.doctor_master')
@section('doctor')
<div class="dashboard-header">
    <h3>Speciality & Services</h3>
    <ul>
        <li>
            <a href="#" class="btn btn-primary prime-btn add-speciality">Add New Speciality</a>
        </li>
    </ul>
</div>

@if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('doctor.services.update') }}" method="POST">
    @csrf
    @method('PUT')

    <div class="accordions" id="list-accord">
        @php $index = 0; @endphp

        @forelse ($groups as $group)
            @php $speciality = $group['speciality']; @endphp
            <!-- Speciality Item -->
            <div class="user-accordion-item">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#speciality-{{ $speciality->id }}">
                    {{ $speciality->name }}
                    <span class="trash delete-trigger" data-target="deleteSpecialityModal{{ $speciality->id }}">Delete</span>
                </a>
                <div class="accordion-collapse collapse show" id="speciality-{{ $speciality->id }}" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-wrap">
                                            <label class="form-label">Speciality</label>
                                            <input type="text" class="form-control" value="{{ $speciality->name }}" disabled>
                                        </div>
                                    </div>
                                </div>
                                @foreach ($group['services'] as $doctorService)
                                    @php $rowIndex = $index++; @endphp
                                    <div class="row service-cont">
                                        <div class="col-md-3">
                                            <div class="form-wrap">
                                                <label class="form-label">Service <span class="text-danger">*</span></label>
                                                <input type="hidden" name="services[{{ $rowIndex }}][service_id]" value="{{ $doctorService->service_id }}">
                                                <input type="text" class="form-control" value="{{ $doctorService->service->name }}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-wrap">
                                                <label class="form-label">Price ($) <span class="text-danger">*</span></label>
                                                <input type="text" inputmode="decimal" class="form-control price-mask" name="services[{{ $rowIndex }}][price]" value="{{ old('services.'.$rowIndex.'.price', $doctorService->price) }}">
                                                @error('services.'.$rowIndex.'.price')
                                                    <div class="text-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-7">
                                            <div class="d-flex align-items-center">
                                                <div class="form-wrap w-100">
                                                    <label class="form-label">About Service</label>
                                                    <input type="text" class="form-control" name="services[{{ $rowIndex }}][description]" value="{{ old('services.'.$rowIndex.'.description', $doctorService->description) }}">
                                                </div>
                                                <div class="form-wrap ms-2">
                                                    <label class="col-form-label d-block">&nbsp;</label>
                                                    <a href="#" class="trash-icon delete-trigger" data-target="deleteServiceModal{{ $doctorService->id }}">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="text-end">
                                <a href="#" class="add-serv more-item mb-0" data-speciality-id="{{ $speciality->id }}">Add New Service</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Speciality Item -->
        @empty
            <!-- Speciality Item (new) -->
            <div class="user-accordion-item speciality-content">
                <a href="#" class="accordion-wrap" data-bs-toggle="collapse" data-bs-target="#speciality-new-0">Speciality</a>
                <div class="accordion-collapse collapse show" id="speciality-new-0" data-bs-parent="#list-accord">
                    <div class="content-collapse">
                        <div class="add-service-info">
                            <div class="add-info">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-wrap">
                                            <label class="form-label">Speciality <span class="text-danger">*</span></label>
                                            <select class="select speciality-select">
                                                <option value="">Select</option>
                                                @foreach ($specialities as $speciality)
                                                    <option value="{{ $speciality->id }}">{{ $speciality->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="#" class="add-serv more-item mb-0" data-speciality-id="">Add New Service</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Speciality Item (new) -->
        @endforelse
    </div>

    <div class="modal-btn text-end">
        <a href="{{ route('doctor.specialities') }}" class="btn btn-gray">Cancel</a>
        <button type="submit" class="btn btn-primary prime-btn">Save Changes</button>
    </div>
</form>

@foreach ($groups as $group)
    <!-- Delete Speciality Modal -->
    <div class="modal fade" id="deleteSpecialityModal{{ $group['speciality']->id }}" aria-hidden="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="form-content p-2">
                        <h4 class="modal-title">Delete Speciality</h4>
                        <p class="mb-4">Are you sure you want to remove all your services under "{{ $group['speciality']->name }}"?</p>
                        <form action="{{ route('doctor.services.speciality.destroy', $group['speciality']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-primary">Delete</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Speciality Modal -->

    @foreach ($group['services'] as $doctorService)
        <!-- Delete Service Modal -->
        <div class="modal fade" id="deleteServiceModal{{ $doctorService->id }}" aria-hidden="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="form-content p-2">
                            <h4 class="modal-title">Delete Service</h4>
                            <p class="mb-4">Are you sure you want to remove "{{ $doctorService->service->name }}" from your services?</p>
                            <form action="{{ route('doctor.services.destroy', $doctorService) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-primary">Delete</button>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cancel</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Delete Service Modal -->
    @endforeach
@endforeach

<script>
    window.SPECIALITIES = @json($specialities->map(fn ($speciality) => ['id' => $speciality->id, 'name' => $speciality->name]));
    window.SERVICES_BY_SPECIALITY = @json($servicesBySpeciality->map(fn ($services) => $services->map(fn ($service) => ['id' => $service->id, 'name' => $service->name])->values()));
    window.NEXT_SERVICE_INDEX = {{ $index }};
</script>
@endsection
