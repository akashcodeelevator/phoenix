@extends('user.layouts.app')

@section('content')
    <div class="container pages">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Order History</h4>
                    </div>
                    <div class="card-body">
                        <table id="orderTable" class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Package Name</th>
                                    <th>Package Amount</th>
                                    <th>Status</th>
                                    <th>Created At</th>
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
                $('#orderTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('user.getorderhistory') }}',
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'pin_type',
                            name: 'users.pin_type'
                        },
                        {
                            data: 'order_amount',
                            name: 'orders.order_amount'
                        },
                        {
                            data: 'status',
                            name: 'orders.status',
                            render: function(data) {
                                return data == 1 ? 'Approved' : (data == 2 ? 'Cancelled' : 'Pending');
                            }
                        },
                        {
                            data: 'created_at',
                            name: 'orders.created_at'
                        },
                    ],
                });

            });
        </script>
    @endpush
@endsection
