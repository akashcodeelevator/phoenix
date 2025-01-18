@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
    <!-- Logout Button -->
    <form method="POST" action="{{ route('user.logout') }}" class="d-inline-block">
        @csrf
        <button type="submit" class="btn btn-danger">Logout</button>
    </form>
@stop
{{-- @extends('user.layouts.app') <!-- Extends a layout (e.g., app layout) --> --}}

@section('content')
<div class="row">
    <div class="col-xxl-8 col-lg-8 d-flex align-items-stretch">
        <div class="card w-100 overflow-hidden rounded-4">
            <div class="card-body position-relative p-4">
                <div class="row">
                    <div class="col-12 col-sm-7">
                        <div class="d-flex align-items-center gap-3 mb-5">
                            <img src="{{ asset('images/users/' . $user->image) }}" alt="User Image" class="rounded-circle p-1 bg-primary" width="60px" height="60px">
                            <div class="">
                                <p class="mb-0 fw-semibold">Good Afternoon</p>
                                <h4 class="fw-semibold mb-0 fs-4 mb-0">{{ $user->company_name }}</h4>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-5">
                            <div class="">
                                <h4 class="mb-1 fw-semibold d-flex align-content-center">
                                    {{ $package->amount??0 }}&nbsp;$<i class="ti ti-arrow-up-right fs-5 lh-base text-success"></i>
                                </h4>
                                <p class="mb-3">My Package</p>
                                <div class="progress mb-0" style="height:5px;">
                                    <div class="progress-bar bg-grd-success" role="progressbar" style="width: {{ $package->progress??0 }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                            <div class="vr"></div>
                            <div class="">
                                <div class="progress mb-0" style="height:5px;">
                                    <div class="progress-bar bg-grd-success" role="progressbar" style="width: {{ $growth_rate }}%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-5">
                        <div class="welcome-back-img pt-4">
                            <img src="{{ asset('User_panel/p32/assets/images/gallery/welcome-back-3.png') }}" height="180" alt="Welcome Back">
                        </div>
                    </div>
                </div><!--end row-->
            </div>
        </div>
    </div>
    <div class="col-xxl-4 col-lg-4">
        <div class="card w-100 rounded-4">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between mb-3">
                    <div class="">
                        <h5 class="mb-0">Referral Link</h5>
                    </div>
                    <div>
                        <p class="text-muted">UserId: {{ $user->company_name }}</p>
                    </div>
                </div>
                <div class="d-flex overflow-hidden flex-column flex-lg-row align-items-start justify-content-around border p-3 rounded-4 mt-3 gap-3">
                    <div class="align-items-center gap-4">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="{{ url('register?ref=' . $user->referral_code) }}" value="{{ url('register?ref=' . $user->referral_code) }}" id="referral_link_left" style="width: 90vw;">
                        </div>
                        <div class="mt-4">
                            <button class="btn btn-primary copyButton" onclick="copyLink1('left')">Copy</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
    {{-- Add here extra stylesheets --}}
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script> 
    // console.log("Hi, I'm using the Laravel-AdminLTE package!"); 
    </script>
@stop