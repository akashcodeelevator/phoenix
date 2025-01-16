@extends('adminlte::page')

@section('title', 'Advanced Settings')

@section('content_header')
    <h1>Advanced Settings</h1>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h3>Update Advanced Settings</h3>
            @if (session('success'))
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    {{ session('error') }}
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
            <form method="POST" action="{{ route('admin.withdrawal_settings.update') }}">
                @csrf

                @foreach ($settings as $setting)
                    <div class="form-group">
                        <label for="{{ $setting->label }}">{{ $setting->name }}</label>

                        @if ($setting->type == 'text')
                            <input type="text" name="{{ $setting->label }}" id="{{ $setting->label }}" class="form-control" 
                                value="{{ $setting->value }}" required>
                        @elseif ($setting->type == 'option')
                            <select name="{{ $setting->label }}" id="{{ $setting->label }}" class="form-control">
                                @foreach (explode(',', $setting->options) as $option)
                                    <option value="{{ $option }}" {{ $setting->value == $option ? 'selected' : '' }}>
                                        {{ ucfirst($option) }}
                                    </option>
                                @endforeach
                            </select>
                        @elseif ($setting->type == 'array')
                            @php
                                $options = json_decode($setting->options, true);
                            @endphp
                            <select name="{{ $setting->label }}" id="{{ $setting->label }}" class="form-control">
                                @foreach ($options as $key => $option)
                                    <option value="{{ $key }}" {{ $setting->value == $key ? 'selected' : '' }}>
                                        {{ $option }}
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                @endforeach

                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>
@endsection
