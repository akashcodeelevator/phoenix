@extends('adminlte::page')

@section('title', 'Users')

@section('content_header')
    <h1>Users</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <div class="row">
                <div class="col-10">
                <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex">
                    <input type="text" name="search" class="form-control" placeholder="Search users..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary ml-2">Search</button>
                </form>
                </div>
                <div class="col-2 text-right">
                <a href="{{ route('admin.users.create') }}" class="btn btn-success float-right">Create User</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover jsgrid-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>User ID</th>
                        <th>First Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>
                            @if ($user->active_status==1)
                                <span class="badge bg-success">Active</span>
                            @elseif($user->active_status ==2)
                                <span class="badge bg-danger">InActive</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                            </td>
                           
                            <td>
                                <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">View</a>
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                                <!-- <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                                </form> -->
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
@endsection
