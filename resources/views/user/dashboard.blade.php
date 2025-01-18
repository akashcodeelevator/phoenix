@extends('user.layouts.app')
@section('content')
    <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
        <div class="breadcrumb-title pe-3">Dashboard</div>
        <div class="ps-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 p-0">
                    <li class="breadcrumb-item"><a href="#"><i class="bx bx-home-alt"></i></a></li>
                    <li class="breadcrumb-item active" aria-current="page">Analysis</li>
                </ol>
            </nav>
        </div>
    </div>
    <div class="row">
        <div class="col-xxl-8 col-lg-8 d-flex align-items-stretch">
            <div class="card w-100 overflow-hidden rounded-4">
                <div class="card-body position-relative p-4">
                    <div class="row">
                        <div class="col-12 col-sm-7">
                            <div class="d-flex align-items-center gap-3 mb-5">
                                <img src="{{ asset('user/assets/images/avatars/01.png') }}" alt="User Image"
                                    class="rounded-circle p-1 bg-primary" width="60" height="60">
                                <div>
                                    <p class="mb-0 fw-semibold">Good Afternoon</p>
                                    <h4 class="fw-semibold mb-0 fs-4">{{ $user->company_name }}</h4>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <div>
                                    <h4 class="mb-1 fw-semibold d-flex align-items-center">
                                        {{ $package->amount ?? 0 }} $<i
                                            class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                    </h4>
                                    <p class="mb-3">My Package</p>
                                    <div class="progress mb-0" style="height: 5px;">
                                        <div class="progress-bar bg-grd-success" role="progressbar"
                                            style="width: {{ $package->progress ?? 0 }}%;" aria-valuenow="25"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="vr"></div>
                                <div>
                                    <h4 class="mb-1 fw-semibold d-flex align-items-center">
                                        Growth Rate: {{ $growth_rate }}%
                                    </h4>
                                    <div class="progress mb-0" style="height: 5px;">
                                        <div class="progress-bar bg-grd-success" role="progressbar"
                                            style="width: {{ $growth_rate }}%;" aria-valuenow="{{ $growth_rate }}"
                                            aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-5">
                            <div class="welcome-back-img pt-4">
                                <img src="{{ asset('user/assets/images/gallery/welcome-back-3.png') }}" height="180"
                                    alt="Welcome Back">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xxl-4 col-lg-4">
            <div class="card w-100 rounded-4">
                <div class="card-body">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <h5 class="mb-0">Referral Link</h5>
                        </div>
                        <div>
                            <p class="text-muted">UserId: {{ $user->company_name }}</p>
                        </div>
                    </div>
                    <div
                        class="d-flex flex-column flex-lg-row align-items-start justify-content-around border p-3 rounded-4 mt-3 gap-3">
                        <div class="form-group w-100">
                            <input type="text" class="form-control"
                                placeholder="{{ url('register?ref=' . $user->referral_code) }}"
                                value="{{ url('register?ref=' . $user->referral_code) }}" id="referral_link_left"
                                readonly>
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary copyButton" onclick="copyLink1('left')">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        @foreach ($incomeData as $index => $income)
            <div class="col-xl-6 col-xxl-4 d-flex align-items-stretch">
                <div class="card w-100 rounded-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between mb-1">
                            <div>
                                <h5 class="mb-0">{{ $income['name'] }}</h5>
                                <p class="mb-0">
                                    {{ $currency ?? '₹' }} {{ number_format($income['wallet_balance'], 2) }}
                                </p>
                            </div>
                        </div>
                        <div class="chart-container2">
                            <div id="{{ $colors[$index] }}"></div>
                        </div>
                        <div class="text-center">
                            {{-- Add any additional details or comments --}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@section('js')
<script src="{{ asset('user/assets/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('user/assets/plugins/peity/jquery.peity.min.js') }}"></script>
<script src="{{ asset('user/assets/plugins/apexchart/apexcharts.min.js') }}"></script>
<script src="{{ asset('user/assets/plugins/chartjs/js/chartjs-custom.js') }}"></script>
@stop
