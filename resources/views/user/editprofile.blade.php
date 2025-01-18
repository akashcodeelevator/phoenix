@extends('user.layouts.app')

@section('content')
    <div class="main-content">
        <!-- Breadcrumb -->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="pe-3">My Account</div>
        </div>

        <!-- Profile Header -->
        <div class="card rounded-4">
            <div class="card-body p-4">
                <div class="position-relative mb-5">
                    <img src="{{ asset('user/assets/images/bg-themes/07.png') }}" class="img-fluid rounded-4 shadow"
                        alt="Cover Image">
                    <div class="profile-avatar position-absolute top-100 start-50 translate-middle">
                        <img src="{{ asset('user/assets/images/avatars/11.png') }}"
                            class="img-fluid rounded-circle p-1 bg-primary" width="170" height="170"
                            alt="Profile Picture">
                    </div>
                </div>
                <div class="profile-info pt-5 d-flex align-items-center justify-content-between">
                    <div>
                        <h3>{{ $user->username }}</h3>
                        <p class="mb-0">{{ $user->email }}</p>
                    </div>
                    <div>
                        <a href="{{ route('user.myprofile') }}" class="btn btn-primary rounded-5 px-4">
                            <i class="material-icons-outlined">person</i>View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Profile Form -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4 text-white">Edit Profile</h4>
                        <form action="{{ url('user/profile/update') }}" method="POST" enctype="multipart/form-data">
                            @csrf
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
                            <!-- Profile Picture -->
                            <div class="row mb-3">
                                <label for="profile_pic" class="col-sm-4 col-form-label text-white">Profile Picture</label>
                                <div class="col-sm-8">
                                    <input type="file" class="form-control" name="profile_pic" id="profile_pic">
                                    @error('profile_pic')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="row mb-3">
                                <label for="name" class="col-sm-4 col-form-label text-white">Full Name</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ $user->name }}" placeholder="Enter your full name">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="row mb-3">
                                <label for="email" class="col-sm-4 col-form-label text-white">Email Address</label>
                                <div class="col-sm-8">
                                    <input type="email" class="form-control" name="email" id="email"
                                        value="{{ $user->email }}" placeholder="Enter your email" readonly>
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Contact Number -->
                            <div class="row mb-3">
                                <label for="mobile" class="col-sm-4 col-form-label text-white">Contact Number</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="mobile" id="mobile"
                                        value="{{ $user->mobile }}" placeholder="Enter your contact number">
                                    @error('mobile')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="row">
                                <div class="col-sm-4"></div>
                                <div class="col-sm-8">
                                    <button type="submit" class="btn btn-primary px-4">Save</button>
                                    <a href="{{ url('user/profile') }}" class="btn btn-danger">Cancel</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
