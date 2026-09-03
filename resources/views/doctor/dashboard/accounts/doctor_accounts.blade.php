@extends('doctor.doctor_master')
@section('doctor')
@php
    $statusBadges = [
        'pending' => 'badge-warning-bg',
        'approved' => 'badge-success-bg',
        'cancelled' => 'badge-danger-bg',
    ];
@endphp
                <div class="accunts-sec">
                    <div class="dashboard-header">
                        <div class="header-back">
                            <h3>Accounts</h3>
                        </div>
                    </div>
                    <div class="account-details-box">
                        <div class="row">
                            <div class="col-xxl-6 col-lg-7">
                                <div class="account-payment-info">
                                    <h4>Statistics</h4>
                                    <div class="row">
                                        <div class="col-lg-4 col-md-6">
                                            <div class="payment-amount">
                                                <h6><i class="fa-solid fa-file-invoice-dollar text-success"></i>Total Balance</h6>
                                                <span>${{ number_format($balance['available'], 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="payment-amount">
                                                <h6><i class="fa-solid fa-money-bill-1 text-orange"></i>Earned</h6>
                                                <span>${{ number_format($balance['earned'], 2) }}</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-md-6">
                                            <div class="payment-amount">
                                                <h6><i class="fa-solid fa-circle-question text-pink"></i>Requested</h6>
                                                <span>${{ number_format($balance['requested'], 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="payment-request">
                                        @php $lastRequest = $payoutRequests->first(); @endphp
                                        <span>Last Payment request : {{ $lastRequest ? $lastRequest->created_at->format('d M Y') : 'No requests yet' }}</span>
                                        @if ($balance['available'] > 0)
                                            <a href="#payment_request" class="btn btn-primary prime-btn" data-bs-toggle="modal">Request Payment</a>
                                        @else
                                            <a href="javascript:void(0);" class="btn btn-primary prime-btn disabled" title="No balance available to request" aria-disabled="true">Request Payment</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-1 d-lg-none d-xxl-block"></div>
                            <div class="col-lg-5">
                                <div class="bank-details-info">
                                    <h3>Bank Details</h3>
                                    @if ($bankAccount)
                                        <ul>
                                            <li>
                                                <h6>Bank Name</h6>
                                                <h5>{{ $bankAccount->bank_name }}</h5>
                                            </li>
                                            <li>
                                                <h6>Account Number</h6>
                                                <h5>{{ $bankAccount->masked_account_number }}</h5>
                                            </li>
                                            <li>
                                                <h6>Branch Name</h6>
                                                <h5>{{ $bankAccount->branch_name ?: '-' }}</h5>
                                            </li>
                                            <li>
                                                <h6>Account Name</h6>
                                                <h5>{{ $bankAccount->account_holder_name }}</h5>
                                            </li>
                                        </ul>
                                    @else
                                        <p class="mb-0">No bank account added yet. Add one so you can request payouts.</p>
                                    @endif
                                    <div class="edit-detail-link">
                                        <a href="#account_details" data-bs-toggle="modal">{{ $bankAccount ? 'Edit Details' : 'Add Account Details' }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="account-detail-table">
                                <!-- Tab Menu -->
                                <nav class="accounts-tab">
                                <ul class="nav nav-tabs-bottom">
                                    <li class="nav-item">
                                        <a class="nav-link active" href="#pat_accounts" data-bs-toggle="tab">Accounts</a>
                                    </li>
                                </ul>
                            </nav>
                            <!-- /Tab Menu -->

                            <!-- Tab Content -->
                            <div class="tab-content pt-0">

                                <!-- Accounts Tab -->
                                <div id="pat_accounts" class="tab-pane fade show active">
                                    <ul class="header-list-btns d-inline-block mb-4">
                                        <li>
                                            <div class="input-block dash-search-input">
                                                <input type="text" class="form-control" placeholder="Search">
                                                <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
                                            </div>
                                        </li>
                                    </ul>
                                    @if ($payoutRequests->isEmpty())
                                        <div class="custom-new-table">
                                            <p class="text-center mb-0 py-4">You haven't requested any payouts yet.</p>
                                        </div>
                                    @else
                                        <div class="custom-new-table">
                                            <div class="table-responsive">
                                                <table class="table table-hover table-center mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Requested Date</th>
                                                            <th>Account No</th>
                                                            <th>Credited On</th>
                                                            <th>Amount</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($payoutRequests as $payoutRequest)
                                                            <tr>
                                                                <td>
                                                                    <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#request_details_modal_{{ $payoutRequest->id }}"><span class="text-blue">#PR-{{ str_pad($payoutRequest->id, 4, '0', STR_PAD_LEFT) }}</span></a>
                                                                </td>
                                                                <td>{{ $payoutRequest->created_at->format('d M Y') }}</td>
                                                                <td>{{ $payoutRequest->masked_account_number }}</td>
                                                                <td>{{ $payoutRequest->processed_at?->format('d M Y') ?: '-' }}</td>
                                                                <td>${{ number_format((float) $payoutRequest->amount, 2) }}</td>
                                                                <td>
                                                                    <span class="badge {{ $statusBadges[$payoutRequest->status->value] }}">{{ $payoutRequest->status->label() }}</span>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:void(0);" class="account-action" data-bs-toggle="modal" data-bs-target="#request_details_modal_{{ $payoutRequest->id }}"><i class="isax isax-link-2"></i></a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        {{ $payoutRequests->links('vendor.pagination.doccure') }}
                                    @endif
                                </div>
                                <!-- /Accounts Tab -->
                            </div>
                            <!-- Tab Content -->
                        </div>
                    </div>
                </div>

<!-- Payment Request Modal -->
<div class="modal fade custom-modal custom-modal-two modal-lg" id="payment_request">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Request</h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span><i class="fa-solid fa-x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('doctor.payout_requests.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="form_context" value="payment_request">
                    <p class="mb-3">Available balance: <strong>${{ number_format($balance['available'], 2) }}</strong></p>
                    <div class="input-block input-block-new">
                        <label class="form-label">Request Amount</label>
                        <input type="text" inputmode="decimal" name="amount" class="form-control" value="{{ old('amount') }}">
                        @error('amount')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new">
                        <label class="form-label">Description</label>
                        <textarea rows="3" name="description" class="form-control">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-set-button">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Submit</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- /Payment Request Modal -->

<!-- Account Details Modal-->
<div class="modal fade custom-modal custom-modal-two modal-lg" id="account_details">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Account Details</h5>
                <button type="button" data-bs-dismiss="modal" aria-label="Close">
                    <span><i class="fa-solid fa-x"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ route('doctor.bank_account.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="form_context" value="account_details">
                    <div class="input-block input-block-new">
                        <label class="form-label">Bank Name</label>
                        <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $bankAccount?->bank_name) }}">
                        @error('bank_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new">
                        <label class="form-label">Branch Name</label>
                        <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name', $bankAccount?->branch_name) }}">
                        @error('branch_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new">
                        <label class="form-label">Account Number</label>
                        <input type="text" name="account_number" class="form-control" value="{{ old('account_number', $bankAccount?->account_number) }}">
                        @error('account_number')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="input-block input-block-new">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="account_holder_name" class="form-control" value="{{ old('account_holder_name', $bankAccount?->account_holder_name) }}">
                        @error('account_holder_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-set-button">
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
<!-- /Account Details Modal-->

@foreach ($payoutRequests as $payoutRequest)
    <!-- Request Details Modal-->
    <div class="modal fade custom-modal custom-modal-two" id="request_details_modal_{{ $payoutRequest->id }}">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Request Details <span class="badge {{ $statusBadges[$payoutRequest->status->value] }}">{{ $payoutRequest->status->label() }}</span></h5>
                    <button type="button" data-bs-dismiss="modal" aria-label="Close">
                        <span><i class="fa-solid fa-x"></i></span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="completed-request">
                        <ul>
                            <li>
                                <h6>ID</h6>
                                <span>#PR-{{ str_pad($payoutRequest->id, 4, '0', STR_PAD_LEFT) }}</span>
                            </li>
                            <li>
                                <h6>Requested on</h6>
                                <span>{{ $payoutRequest->created_at->format('d M Y') }}</span>
                            </li>
                            <li>
                                <h6>{{ $payoutRequest->status === \App\Enums\PayoutRequestStatus::Cancelled ? 'Cancelled Date' : 'Credited Date' }}</h6>
                                <span>{{ $payoutRequest->processed_at?->format('d M Y') ?: 'Pending' }}</span>
                            </li>
                            <li>
                                <h6>Amount</h6>
                                <span class="text-blue">${{ number_format((float) $payoutRequest->amount, 2) }}</span>
                            </li>
                        </ul>
                        <div class="bank-detail">
                            <h4>Bank Details</h4>
                            <div class="accont-information">
                                <h6>Name</h6>
                                <span>{{ $payoutRequest->bank_name }}</span>
                            </div>
                            <div class="accont-information">
                                <h6>Account No</h6>
                                <span>{{ $payoutRequest->masked_account_number }}</span>
                            </div>
                            @if ($payoutRequest->branch_name)
                                <div class="accont-information">
                                    <h6>Branch</h6>
                                    <span>{{ $payoutRequest->branch_name }}</span>
                                </div>
                            @endif
                        </div>
                        @if ($payoutRequest->description)
                            <div class="request-des">
                                <h4>Request Description</h4>
                                <p>{{ $payoutRequest->description }}</p>
                            </div>
                        @endif
                        <a href="javascript:void(0);" class="btn btn-primary prime-btn w-100" data-bs-dismiss="modal">Close</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Request Details Modal-->
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
