@extends('user.layouts.app')

@section('content')
    <div class="card">
        <div class="card-body text-white">
            <h3 class="mb-4 text-white">NEW SUPPORT TICKET</h3>
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
            <p class="text-muted mb-4 text-white">
                Would you like to speak to one of our financial advisers over the phone? Just submit your details, and we'll
                be in touch shortly. You can also email us if you would prefer.
            </p>
            <div>
                <form action="{{ route('user.supportsubmit') }}" method="post">
                    @csrf <!-- Include CSRF token for security -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group mt-3">
                                <label for="exampleInputname">Name</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter Name"
                                    value="" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="exampleInputEmail">Email</label>
                                <input type="email" id="email_address" name="email_address" class="form-control"
                                    value="" placeholder="Enter Email" required>
                            </div>
                            <div class="form-group mt-3">
                                <label for="description">Description</label>
                                <textarea required class="form-control" rows="4" name="description" id="description"></textarea>
                            </div>
                            <div class="email_buttons mt-3">
                                <button type="submit" class="btn btn-primary" name="send">Send</button>
                                <a href="{{ route('user.support') }}" class="btn btn-primary">Reset</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
