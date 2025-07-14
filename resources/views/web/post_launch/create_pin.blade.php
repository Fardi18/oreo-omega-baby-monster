@extends('_template_web.master')

@php
    $pagetitle = 'Set PIN';
    $locale = app()->getLocale();
@endphp

@section('style')
    <style>
        .landing-page {
            max-width: 400px;
            margin: 0 auto;
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            font-family: Arial, sans-serif;
            text-align: center;
        }

        .landing-page h1 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .landing-page p {
            font-size: 16px;
            color: #333;
            margin: 20px 0;
        }

        .landing-page button {
            background-color: black;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 20px;
            width: 100%;
            font-size: 16px;
            margin-top: 10px;
        }
    </style>
@endsection

@section('title', $pagetitle)

@section('content')
    <div class="landing-page">
        <div>
            <h1>Set Your PIN</h1>
            <p>Please set your 4-digit PIN for secure access</p>
        </div>

        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card shadow-lg border-0 rounded-3">
                        <div class="card-body p-4 p-md-5">
                            <form method="POST"
                                action="{{ route('web.create_pin_process', ['market' => $market, 'lang' => $lang, 'id' => $raw_id]) }}"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="form-floating mb-4">
                                    <input type="password" name="pin" id="pin" maxlength="4"
                                        class="form-control form-control-lg" required pattern="\d{4}" inputmode="numeric"
                                        placeholder="••••">
                                    <p>Enter a secure 4-digit PIN</p>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">
                                    {{ __('Save PIN') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
