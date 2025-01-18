@extends('user.layouts.app')

@section('content')
    <div class="main-content">
        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="pe-3">My Account</div>

        </div>
        <!--end breadcrumb-->

        <div class="col-xxl-12 col-lg-12 d-flex align-items-stretch">
            <div class="card w-100 overflow-hidden rounded-4">
                <div class="card-body position-relative p-4">
                    <div class="row">
                        <!-- Display Success Message -->
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif

                        <!-- Display General Error Message -->
                        @if ($errors->has('error'))
                            <div class="alert alert-danger">
                                {{ $errors->first('error') }}
                            </div>
                        @endif
                        <div class="col-12 col-sm-7">
                            <div class="d-flex align-items-center gap-3 mb-5">
                                <img src="{{ asset('user/assets/images/avatars/11.png') }}" alt="images" width="110">
                                <div>
                                    <h5 class="mb-0 fw-semibold">Good Afternoon</h5>
                                    <h4 class="fw-semibold mb-0 fs-4">&nbsp;</h4>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <div>
                                    <h4 class="mb-1 fw-semibold d-flex align-content-center">
                                        <span style="color:{{ $user->active_status == 0 ? 'red' : 'green' }};">
                                            {{ $user->active_status == 0 ? 'Inactive' : 'Active' }}
                                        </span>
                                        <i class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                    </h4>
                                    <p class="mb-3">My Status</p>
                                    <div class="progress mb-0" style="height:5px;">
                                        <div class="progress-bar bg-grd-danger" role="progressbar" style="width: 60%;"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                                <div class="vr"></div>
                                <div>
                                    <h4 class="mb-1 fw-semibold d-flex align-content-center">
                                        {{ $user->username }}
                                        <i class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                    </h4>
                                    <p class="mb-3">User Id</p>
                                    <div class="progress mb-0" style="height:5px;">
                                        <div class="progress-bar bg-grd-success" role="progressbar" style="width: 60%;"
                                            aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
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

        <!--begin::details View-->
        <div class="card mb-5 mb-xl-10" id="kt_profile_details_view">
            <!--begin::Card header-->
            <div class="card-header cursor-pointer">
                <!--begin::Card title-->
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Profile Details</h3>
                </div>
                <!--end::Card title-->
                <!--begin::Action-->
                <a href="{{ route('user.editprofile') }}" class="btn btn-sm btn-primary align-self-center mt-3">Edit
                    Profile</a>
                <!--end::Action-->
            </div>
            <!--end::Card header-->

            <!--begin::Card body-->
            <div class="card-body p-9">
                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold" style="color:#ddb12c;">Sponsor</label>
                    <div class="col-lg-8 fv-row">
                        <span
                            class="fw-semibold text-gray-800 fs-6">{{ $user->ReferralUser->username ?? 'companyname' }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold" style="color:#ddb12c;">User address</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $user->eth_address ?? 'N/A' }}</span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold" style="color:#ddb12c;">
                        Status
                        <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip"
                            title="Country of origination"></i>
                    </label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-primary text-800"><span
                                style="color:{{ $user->active_status == 0 ? 'red' : 'green' }};">
                                {{ $user->active_status == 0 ? 'Inactive' : 'Active' }}
                            </span></span>
                    </div>
                </div>

                <div class="row mb-7">
                    <label class="col-lg-4 fw-semibold" style="color:#ddb12c;">Activation Date</label>
                    <div class="col-lg-8">
                        <span class="fw-bold fs-6 text-gray-800">{{ $user->created_at ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
            <!--end::Card body-->
        </div>
        <!--end::details View-->
    </div>
@endsection
