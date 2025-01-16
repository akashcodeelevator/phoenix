@extends('adminlte::page')

@section('title', 'Transaction Reports')

@section('content_header')
    <h1>Transaction Reports</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <form id="filterForm" method="GET" action="{{ route('admin.reports.getReport') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label for="tx_type">Transaction Type</label>
                        <select name="tx_type" class="form-control">
                            <option value="">All</option>
                            <option value="admin_credit">Admin Credit</option>
                            <option value="fund_request">Fund Request</option>
                            <option value="income">Income</option>
                            <option value="topup">Topup</option>
                            <option value="fund_transfer">Fund Transfer</option>

                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="debit_credit">Debit/Credit</label>
                        <select name="debit_credit" class="form-control">
                            <option value="">All</option>
                            <option value="debit">Debit</option>
                            <option value="credit">Credit</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="wallet_type">Wallet Type</label>
                        <select name="wallet_type" class="form-control">
                            <option value="">All</option>
                            <option value="main_wallet">Main Wallet</option>
                            <option value="fund_wallet">Fund Wallet</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date">Start Date</label>
                        <input type="date" name="start_date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">End Date</label>
                        <input type="date" name="end_date" class="form-control">
                    </div>
                    <div class="col-md-12 mt-3">
                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div id="reportTable">
                <!-- Transactions table will be dynamically populated using JavaScript -->
            </div>
            <div id="pagination">
                <!-- Pagination links will be dynamically populated here -->
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    document.getElementById('filterForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const url = this.action;
    const params = new URLSearchParams(new FormData(this));

    fetch(`${url}?${params}`)
        .then(response => response.json())
        .then(data => {
            let table = `<table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Debit/Credit</th>
                        <th>Date</th>
                        <th>Remark</th>
                        <th>Status</th>
                        <th>User Code</th>
                        <th>Wallet Type</th>
                    </tr>
                </thead>
                <tbody>`;

            // Add the table rows dynamically
            data.data.forEach(transaction => {
                table += `<tr>
                    <td>${transaction.amount}</td>
                    <td>${transaction.debit_credit}</td>
                    <td>${transaction.date}</td>
                    <td>${transaction.remark}</td>
                    <td>${transaction.status}</td>
                    <td>${transaction.username}</td>
                    <td>${transaction.wallet_type}</td>
                </tr>`;
            });

            table += `</tbody></table>`;

            // Check if reportTable exists
            const reportTableElement = document.getElementById('reportTable');
            if (reportTableElement) {
                reportTableElement.innerHTML = table;
            }

            // Pagination links
            let pagination = `<nav><ul class="pagination">`;

            // Previous page link
            if (data.prev_page_url) {
                pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${data.prev_page_url}')">&laquo; Previous</a></li>`;
            } else {
                pagination += `<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>`;
            }

            // Current page link
            for (let i = 1; i <= data.last_page; i++) {
                pagination += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="loadPage('${data.path}?page=${i}')">${i}</a>
                </li>`;
            }

            // Next page link
            if (data.next_page_url) {
                pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${data.next_page_url}')">Next &raquo;</a></li>`;
            } else {
                pagination += `<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>`;
            }

            pagination += `</ul></nav>`;

            // Check if pagination exists
            const paginationElement = document.getElementById('pagination');
            if (paginationElement) {
                paginationElement.innerHTML = pagination;
            }
        });
    });

    // Function to load a new page via Ajax
    function loadPage(url) {
        fetch(url)
            .then(response => response.json())
            .then(data => {
                let table = `<table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Debit/Credit</th>
                            <th>Date</th>
                            <th>Remark</th>
                            <th>Status</th>
                            <th>User Code</th>
                            <th>Wallet Type</th>
                        </tr>
                    </thead>
                    <tbody>`;

                // Add the table rows dynamically
                data.data.forEach(transaction => {
                    table += `<tr>
                        <td>${transaction.amount}</td>
                        <td>${transaction.debit_credit}</td>
                        <td>${transaction.date}</td>
                        <td>${transaction.remark}</td>
                        <td>${transaction.status}</td>
                        <td>${transaction.username}</td>
                        <td>${transaction.wallet_type}</td>
                    </tr>`;
                });

                table += `</tbody></table>`;
                document.getElementById('reportTable').innerHTML = table;

                // Update pagination links
                let pagination = `<nav><ul class="pagination">`;

                // Previous page link
                if (data.prev_page_url) {
                    pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${data.prev_page_url}')">&laquo; Previous</a></li>`;
                } else {
                    pagination += `<li class="page-item disabled"><span class="page-link">&laquo; Previous</span></li>`;
                }

                // Current page link
                for (let i = 1; i <= data.last_page; i++) {
                    pagination += `<li class="page-item ${i === data.current_page ? 'active' : ''}">
                        <a class="page-link" href="#" onclick="loadPage('${data.path}?page=${i}')">${i}</a>
                    </li>`;
                }

                // Next page link
                if (data.next_page_url) {
                    pagination += `<li class="page-item"><a class="page-link" href="#" onclick="loadPage('${data.next_page_url}')">Next &raquo;</a></li>`;
                } else {
                    pagination += `<li class="page-item disabled"><span class="page-link">Next &raquo;</span></li>`;
                }

                pagination += `</ul></nav>`;
                document.getElementById('pagination').innerHTML = pagination;
            });
    }

</script>

@endsection
