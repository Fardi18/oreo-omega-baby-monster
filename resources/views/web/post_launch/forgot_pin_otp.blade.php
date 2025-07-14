@extends('_template_web.master')

@php
    $pagetitle = 'Verify OTP PIN';
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

        .otp-container {
            max-width: 320px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background-color: #f9f9f9;
        }

        h3 {
            margin-top: 0;
        }

        .box {
            border: 2px solid #999;
            margin: 15px auto;
            padding: 15px;
            width: 90%;
            font-size: 16px;
            background-color: white;
        }

        .otp-input {
            width: 100%;
            padding: 10px;
            border-radius: 20px;
            border: 1px solid #ccc;
            margin: 10px 0;
            padding-left: 40px;
            position: relative;
        }

        .submit-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 0;
            width: 100%;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
        }

        .resend-btn {
            background-color: white;
            color: black;
            border: 1px solid black;
            padding: 10px 0;
            width: 100%;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            margin-bottom: 10px;
            display: none;
        }

        .footer {
            font-size: 12px;
            margin-top: 20px;
            color: #555;
        }

        .footer a {
            color: #555;
            text-decoration: none;
            margin: 0 5px;
        }

        .alert {
            color: red;
            font-size: 14px;
            margin: 10px 0;
            padding: 10px;
            background-color: #fee;
            border-radius: 5px;
        }
    </style>
@endsection

@section('content')
    <section>
        <div class="otp-container">
            <h3>Insert the OTP</h3>

            <div class="box">OTP has been sent!</div>

            @if (session('success'))
                <div class="box">{{ session('success') }}</div>
            @else
                <div class="box">Check your email and<br>enter the code below</div>
            @endif

            @if ($errors->any())
                <div class="alert">
                    @foreach ($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form method="POST"
                action="{{ route('web.forgot_pin_otp_process', ['market' => $market, 'lang' => $lang, 'id' => $raw_id]) }}">
                @csrf
                <div style="position: relative;">
                    <input type="number" placeholder="OTP Code" name="otp" id="otp" class="otp-input"
                        maxlength="4" required value="{{ old('otp') }}">
                </div>

                <button type="submit" class="submit-btn">Submit</button>
            </form>

            <form method="POST"
                action="{{ route('web.resend_otp', ['market' => $market, 'lang' => $lang, 'id' => $raw_id]) }}">
                @csrf
                <button type="submit" class="resend-btn" id="resendBtn">Resend OTP</button>
            </form>

            <div class="footer">
                Terms & Condition | Privacy Polish | Cookie Policy | Contact Us<br>
                © 2023 Mondelēz International - All rights reserved
            </div>
        </div>
    </section>
@endsection

@section('footer-script')
    <script>
        // Limit OTP input to 4 digits
        document.getElementById('otp').addEventListener('input', function(e) {
            if (this.value.length > 4) {
                this.value = this.value.slice(0, 4);
            }
        });

        // Show resend button if OTP is expired
        @if ($errors->has('otp') && $errors->first('otp') == 'OTP has been expired')
            document.getElementById('resendBtn').style.display = 'block';
        @endif
    </script>
@endsection
