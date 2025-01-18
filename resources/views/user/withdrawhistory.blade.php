@extends('user.layouts.app')

@section('content')
    <div class="container pages">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Fund Requests</h4>
                    </div>
                    <div class="card-body">
                        <table id="fundRequestsTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Remark</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <link href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css" rel="stylesheet">
        <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

        <script>
            $(document).ready(function() {
                $('#fundRequestsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('user.getwithdrawhistory') }}',
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'username',
                            name: 'users.username'
                        },
                        {
                            data: 'name',
                            name: 'users.name'
                        },
                        {
                            data: 'amount',
                            name: 'transaction.amount'
                        },
                        {
                            data: 'status',
                            name: 'transaction.status',
                            render: function(data) {
                                return data == 1 ? 'Approved' : (data == 2 ? 'Cancelled' : 'Pending');
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'transaction.created_at'
                        },
                        {
                            data: 'remark',
                            name: 'transaction.remark'
                        },
                    ],
                });

            });
        </script>
    @endpush
@endsection
