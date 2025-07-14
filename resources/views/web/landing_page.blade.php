@extends('_template_web.master')

@php
    $pagetitle = 'Home';
    $locale = app()->getLocale();
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
    </style>
@endsection

@section('content')
    <section>
        <div class="landing-page">
            <h2>Welcome to Oreo Baby Monster</h2>
            <p>{{ __('landing_page.subtitle-1') }}<br>{{ __('landing_page.subtitle-2') }}</p>

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

            <ul>
                <li><a href="{{ route('web.register_pre_launch_page', ['market' => 'en', 'lang' => 'en']) }}">Pre-Launch Registration</a></li>
                <li><a href="{{ route('web.register_post_launch_page', ['market' => 'en', 'lang' => 'en']) }}">Post-Launch Registration</a></li>
            </ul>
        </div>
    </section>
@endsection

@section('footer-script')
@endsection
