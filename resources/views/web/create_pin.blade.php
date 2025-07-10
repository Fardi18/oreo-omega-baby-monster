@extends('_template_web.master')

@php
    $pagetitle = 'Set PIN';
    $locale = app()->getLocale();
@endphp

@section('title', $pagetitle)

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">{{ __('Set Your 4-Digit PIN') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST"
                            action="{{ route('web.set_pin', ['market' => $market, 'lang' => $lang, 'id' => $raw_id]) }}">
                            @csrf

                            <div class="form-group mb-3">
                                <label for="pin">{{ __('PIN') }}</label>
                                <input type="password" name="pin" id="pin" maxlength="4" class="form-control"
                                    required pattern="\d{4}" inputmode="numeric" placeholder="••••">
                                <small class="form-text text-muted">{{ __('Enter a secure 4-digit PIN.') }}</small>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">{{ __('Save PIN') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
