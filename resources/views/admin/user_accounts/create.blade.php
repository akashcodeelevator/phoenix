@extends('adminlte::page')

@section('title', 'Create User Account')

@section('content_header')
       <h1></h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Add User Account</h3>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.user_accounts.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label for="u_code">User</label>
                    <select name="u_code" id="u_code" class="form-control" required>
                        <option value="" selected>Select a user</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->id }} - {{ $user->name }} ({{ $user->username }})
                            </option>
                        @endforeach
                    </select>
                </div>


                <div class="form-group">
                    <label for="kyc_status">KYC Status</label>
                    <select name="kyc_status" id="kyc_status" class="form-control" required>
                        <option value="pending" {{ old('kyc_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ old('kyc_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="rejected" {{ old('kyc_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kyc_remark">KYC Remark</label>
                    <textarea name="kyc_remark" id="kyc_remark" class="form-control" rows="3">{{ old('kyc_remark') }}</textarea>
                </div>

                <div class="form-group">
                    <label for="document_type">Document Type</label>
                    <select name="document_type" id="document_type" class="form-control" required>
                        <option value="pan" {{ old('document_type') == 'pan' ? 'selected' : '' }}>PAN</option>
                        <option value="adhaar" {{ old('document_type') == 'adhaar' ? 'selected' : '' }}>Adhaar</option>
                        <option value="both" {{ old('document_type', $userAccount->account_type ?? '') == 'both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>

                <div id="pan_fields" style="display: none;">
                    <h4>PAN Details</h4>
                    <div class="form-group">
                        <label for="pan_no">PAN Number</label>
                        <input type="text" name="pan_no" id="pan_no" class="form-control" value="{{ old('pan_no') }}">
                    </div>
                    <div class="form-group">
                        <label for="pan_image">PAN Image</label>
                        <input type="file" name="pan_image" id="pan_image" class="form-control">
                    </div>
                </div>

                <div id="adhaar_fields" style="display: none;">
                    <h4>Adhaar Details</h4>
                    <div class="form-group">
                        <label for="adhaar_no">Adhaar Number</label>
                        <input type="text" name="adhaar_no" id="adhaar_no" class="form-control" value="{{ old('adhaar_no') }}">
                    </div>
                    <div class="form-group">
                        <label for="adhaar_image">Adhaar Image</label>
                        <input type="file" name="adhaar_image" id="adhaar_image" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="adhaar_back_image">Adhaar Back Image</label>
                        <input type="file" name="adhaar_back_image" id="adhaar_back_image" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('document_type').addEventListener('change', function () {
            let docType = this.value;
            document.getElementById('pan_fields').style.display = (docType === 'pan' || docType === 'both') ? 'block' : 'none';
            document.getElementById('adhaar_fields').style.display = (docType === 'adhaar' || docType === 'both') ? 'block' : 'none';
        });

        // Trigger change event on page load
        document.getElementById('document_type').dispatchEvent(new Event('change'));
    </script>
@endsection
