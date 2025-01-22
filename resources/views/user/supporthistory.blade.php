@extends('user.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body text-white">
            <h3 class="text-white">Support Detail</h3>
            <div class="table-responsive">
                <table class="table table-nowrap mb-0">
                    <tbody>
                        <tr>
                            <th scope="row">Not Replied Inquiry</th>
                            <td class="text-danger">{{ $count_support->notRepliedCount }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Replied Inquiry</th>
                            <td class="text-success">{{ $count_support->repliedCount  }}</td>
                        </tr>
                        <tr>
                            <th scope="row">Total Inquiry</th>
                            <td class="text-success">{{ $count_support->totalCount  }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="pages mt-3">
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
                                    <th>Ticket Id</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Reply</th>
                                    <th>Reply Message</th>
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
                    ajax: '{{ route('user.getsupporthistory') }}',
                    columns: [{
                            data: 'id',
                            name: 'id'
                        },
                        {
                            data: 'ticket',
                            name: 'support.ticket'
                        },
                        {
                            data: 'message',
                            name: 'support.message'
                        },
                        {
                            data: 'status',
                            name: 'support.status',
                        },
                        {
                            data: 'reply',
                            name: 'support.reply'
                        },
                        {
                            data: 'replymessage',
                            name: 'support.replymessage'
                        },
                        {
                            data: 'created_at',
                            name: 'support.created_at'
                        },
                    ],
                });

            });
        </script>
    @endpush
@endsection
