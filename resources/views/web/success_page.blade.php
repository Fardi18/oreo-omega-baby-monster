@extends('_template_web.master')

@php
    $pagetitle = 'Successfully Registered';
    $locale = app()->getLocale();
@endphp

@section('title', $pagetitle)

@section('style')
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            text-align: center;
        }

        .confirmation-container {
            max-width: 320px;
            margin: 0 auto;
            padding: 20px;
            background: #f2f2f2;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        .confirmation-box {
            border: 2px solid #999;
            background: white;
            margin: 20px auto;
            padding: 15px;
            width: 90%;
            font-size: 16px;
            line-height: 1.5;
        }

        .confirmation-box strong {
            display: block;
            font-weight: bold;
            margin: 8px 0;
        }

        .footer {
            font-size: 12px;
            color: #555;
            margin-top: 30px;
        }

        .footer a {
            text-decoration: none;
            color: #555;
            margin: 0 4px;
        }

        .mondelēz {
            color: #7a469b;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="confirmation-container">
            <div class="confirmation-box">
                You’re all set!
                <strong>OR</strong>
                You’re officially on the VIP list!
            </div>

            <div class="confirmation-box">
                Stay tuned!
            </div>

            @if ($user->type == 'post-launch' && $user->pin == null)
                <div class="confirmation-box">
                    <strong>Next Step:</strong>
                    <p>To complete your registration, please create a 4-digit PIN.</p>
                    <a href="{{ route('web.create_pin_page', ['market' => $market, 'lang' => $lang, 'id' => $raw_id]) }}"
                        class="btn btn-primary">Create PIN</a>
                </div>
            @endif

            <div class="footer">
                Terms & Condition | Privacy Polish | Cookie Policy | Contact Us<br>
                © 2025 Mondelēz International – All rights reserved
            </div>
        </div>
    </section>
@endsection

@section('footer-script')
    {{--  --}}
@endsection
