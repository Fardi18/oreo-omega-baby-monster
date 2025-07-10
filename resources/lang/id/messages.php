<?php

return [
    'validation' => [
        'first_name' => [
            'required' => 'Nama depan wajib diisi.'
        ],
        'last_name' => [
            'required' => 'Nama belakang wajib diisi.'
        ],
        'email' => [
            'email' => 'Email harus berupa alamat email yang valid.',
            'unique' => 'Email sudah digunakan.',
            'blocked' => 'Alamat email ini telah diblokir.',
            'used_in_another_market' => 'Email ini sudah digunakan di pasar lain.'
        ],
        'phone_number' => [
            'unique' => 'Nomor telepon sudah digunakan.',
            'invalid' => 'Nomor telepon tidak valid.',
            'blocked' => 'Nomor telepon ini telah diblokir.',
            'used_in_another_market' => 'Nomor telepon ini sudah digunakan di pasar lain.'
        ],
        'date_of_birth' => [
            'date' => 'Tanggal lahir harus berupa tanggal yang valid.',
            'future' => 'Tanggal lahir harus di masa lalu.',
            'underage' => 'Anda harus berusia minimal 18 tahun untuk mendaftar.'
        ],
        'market_id' => [
            'required' => 'Pasar wajib diisi.',
            'not_found' => 'Pasar tidak ditemukan'
        ],
        'otp' => [
            'required' => 'OTP wajib diisi.',
            'digits' => 'OTP harus 4 digit.',
            'invalid' => 'OTP tidak valid',
            'expired' => 'OTP telah kedaluwarsa'
        ],
        'pin' => [
            'required' => 'PIN wajib diisi.',
            'digits' => 'PIN harus 4 digit.',
        ],
    ],
    'errors' => [
        'invalid_input' => 'Input tidak valid terdeteksi.',
        'verify_otp_error' => 'Terjadi kesalahan saat memverifikasi OTP.',
        'resend_otp_error' => 'Gagal mengirim ulang OTP. Silakan coba lagi.',
        'invalid_id' => 'ID tidak valid',
        'user_not_found' => 'Pengguna tidak ditemukan',
        'user_already_verified' => 'Pengguna sudah diverifikasi',
        'user_already_active' => 'Pengguna sudah aktif',
        'invalid_access' => 'Akses tidak valid'
    ],
    'success' => [
        'otp_email' => 'Periksa email Anda dan masukkan kode di bawah ini',
        'otp_phone' => 'Periksa pesan Anda dan masukkan kode di bawah ini',
        'otp_resent_email' => 'OTP baru telah dikirim ke email Anda',
        'otp_resent_phone' => 'OTP baru telah dikirim ke nomor telepon Anda'
    ],
];
