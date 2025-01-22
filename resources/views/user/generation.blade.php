@extends('user.layouts.app')

@section('content')
<div class="container">
    <h2>Generation List</h2>
    <table id="usersTable" class="table table-bordered">
        <thead>
            <tr>
                <th>Sr No.</th>
                <th>Name</th>
                <th>Username</th>
                <th>Mobile</th>
                <th>Join Date</th>
                <th>Status</th>
                <th>Level</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
</div>

@push('scripts')
<link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
    $(document).ready(function() {
        $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('user.generationhistory') }}',
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'username', name: 'username' },
                { data: 'mobile', name: 'mobile' },
                { data: 'created_at', name: 'created_at' },
                { data: 'status', name: 'status', render: function(data) {
                        return data == 1 ? 'Active' : 'Inactive';
                    }
                },
                { data: 'level', name: 'level' },
            ],
        });
    });
</script>
@endpush
@endsection
