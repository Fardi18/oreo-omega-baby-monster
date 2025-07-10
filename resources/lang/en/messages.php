<?php

return [
    'validation' => [
        'first_name' => [
            'required' => 'First name is required.'
        ],
        'last_name' => [
            'required' => 'Last name is required.'
        ],
        'email' => [
            'email' => 'Email must be a valid email address.',
            'unique' => 'Email has already been taken.',
            'blocked' => 'This email address has been blocked.',
            'used_in_another_market' => 'This email is already used in another market.'
        ],
        'phone_number' => [
            'unique' => 'Phone number has already been taken.',
            'invalid' => 'Phone number is invalid.',
            'blocked' => 'This phone number has been blocked.',
            'used_in_another_market' => 'This phone number is already used in another market.'
        ],
        'date_of_birth' => [
            'date' => 'Date of birth must be a valid date.',
            'future' => 'Date of birth must be in the past.',
            'underage' => 'You must be at least 18 years old to register.'
        ],
        'market_id' => [
            'required' => 'Market is required.',
            'not_found' => 'Market not Found'
        ],
        'otp' => [
            'required' => 'OTP is required.',
            'digits' => 'OTP must be 4 digits.',
            'invalid' => 'Invalid OTP',
            'expired' => 'OTP has been expired'
        ],
        'pin' => [
            'required' => 'PIN is required.',
            'digits' => 'PIN must be 4 digits.',
        ],
    ],
    'errors' => [
        'invalid_input' => 'Invalid input detected.',
        'verify_otp_error' => 'An error occurred while verifying the OTP.',
        'resend_otp_error' => 'Failed to resend OTP. Please try again.',
        'invalid_id' => 'Invalid ID',
        'user_not_found' => 'User not found',
        'user_already_verified' => 'User already verified',
        'user_already_active' => 'User already active',
        'invalid_access' => 'Invalid access'
    ],
    'success' => [
        'otp_email' => 'Check your email and enter the code below',
        'otp_phone' => 'Check your message and enter the code below',
        'otp_resent_email' => 'New OTP has been sent to your email',
        'otp_resent_phone' => 'New OTP has been sent to your phone'
    ],
];
