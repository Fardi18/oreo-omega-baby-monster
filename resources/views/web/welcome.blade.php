@extends('_template_web.master')

@php
    $pagetitle = 'Welcome to Oreo Omega';
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
    </style>
@endsection

@section('title', $pagetitle)

@section('content')
    <div class="landing-page">
        <div>
            <h1>Welcome to Oreo Omega Baby Monster</h1>
            <p>Test</p>
        </div>
    </div>
@endsection
