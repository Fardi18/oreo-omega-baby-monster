@extends('_template_web.master')

@php
    $pagetitle = 'Login';
@endphp

@section('title', $pagetitle)

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

        .landing-page h2 {
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        .landing-page p {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }

        .landing-page input[type="text"],
        .landing-page input[type="password"],
        .landing-page input[type="email"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
            color: #000;
            background-color: #fff;
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

        .landing-page button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
    </style>
@endsection

@section('content')
    <section>
        <div class="landing-page">
            <h2>Login</h2>
            <p>Use your email address or phone number and PIN to login.</p>

            @if ($errors->any())
                <script>
                    alert(`{!! implode('\n', $errors->all()) !!}`);
                </script>
            @endif

            @if (session('success'))
                <script>
                    alert(`{{ session('success') }}`);
                </script>
            @endif

            <form
                action="{{ route('web.login_process', ['market' => request()->segment(1), 'lang' => request()->segment(2)]) }}"
                method="POST">
                @csrf
                <div>
                    <input type="text" placeholder="Your Email or Phone Number" name="email_or_phone_number" id="email_or_phone_number" required
                        value="{{ old('email_or_phone_number') }}">
                </div>

                <div>
                    <input type="password" placeholder="Your PIN" name="pin" id="pinField" required
                        value="{{ old('pin') }}" max="4">
                </div>

                <button type="submit" id="submit-button">
                    Submit
                </button>
            </form>
        </div>
    </section>
@endsection

@section('footer-script')
@endsection
