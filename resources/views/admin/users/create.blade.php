@extends('adminlte::page')

@section('title', 'Create User')

@section('content_header')
    <h1>Create User</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="mobile" name="mobile" id="mobile" class="form-control" value="{{ old('mobile') }}" required>
                    @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="active_status">Status</label>
                    <select name="active_status" id="active_status" class="form-control" required>
                        <option value="1" {{ old('active_status') == 1 ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ old('active_status') == 2 ? 'selected' : '' }}>Inactive</option>
                        <option value="3" {{ old('active_status') == 3 ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('active_status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                    @error('password')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="referral_code">Referral Code</label>
                    <input type="referral_code" name="referral_code" id="referral_code" class="form-control" value="{{ old('referral_code') }}" required>
                    @error('referral_code')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                    
                <button type="submit" class="btn btn-success">Create</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
