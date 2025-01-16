@extends('adminlte::page')

@section('title', 'Edit User')

@section('content_header')
    <h1>Edit User</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="mobile">Mobile</label>
                    <input type="mobile" name="mobile" id="mobile" class="form-control" value="{{ old('mobile', $user->mobile) }}" required>
                    @error('mobile')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="eth_address">Withdraw address</label>
                    <input type="eth_address" name="eth_address" id="eth_address" class="form-control" value="{{ old('eth_address', $user->eth_address) }}">
                    @error('eth_address')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="active_status">Status</label>
                    <select name="active_status" id="active_status" class="form-control" required>
                        <option value="1" {{ old('active_status', $user->active_status) == 1 ? 'selected' : '' }}>Active</option>
                        <option value="2" {{ old('active_status', $user->active_status) == 2 ? 'selected' : '' }}>Inactive</option>
                        <option value="3" {{ old('active_status', $user->active_status) == 3 ? 'selected' : '' }}>Pending</option>
                    </select>
                    @error('active_status')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
@endsection
