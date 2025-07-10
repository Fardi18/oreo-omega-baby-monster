<?php

return [
    'validation' => [
        'first_name' => [
            'required' => 'Tên không được để trống.'
        ],
        'last_name' => [
            'required' => 'Họ không được để trống.'
        ],
        'email' => [
            'email' => 'Email phải là địa chỉ email hợp lệ.',
            'unique' => 'Email đã được sử dụng.',
            'blocked' => 'Địa chỉ email này đã bị chặn.',
            'used_in_another_market' => 'Email này đã được sử dụng trong một thị trường khác.'
        ],
        'phone_number' => [
            'unique' => 'Số điện thoại đã được sử dụng.',
            'invalid' => 'Số điện thoại không hợp lệ.',
            'blocked' => 'Số điện thoại này đã bị chặn.',
            'used_in_another_market' => 'Số điện thoại này đã được sử dụng trong một thị trường khác.'
        ],
        'date_of_birth' => [
            'date' => 'Ngày sinh phải là ngày hợp lệ.',
            'future' => 'Ngày sinh phải là ngày trong quá khứ.',
            'underage' => 'Bạn phải đủ 18 tuổi để đăng ký.'
        ],
        'market_id' => [
            'required' => 'Thị trường không được để trống.',
            'not_found' => 'Không tìm thấy thị trường'
        ],
        'otp' => [
            'required' => 'Mã OTP không được để trống.',
            'digits' => 'Mã OTP phải có 4 chữ số.',
            'invalid' => 'Mã OTP không hợp lệ',
            'expired' => 'Mã OTP đã hết hạn'
        ],
        'pin' => [
            'required' => 'Mã PIN không được để trống.',
            'digits' => 'Mã PIN phải có 4 chữ số.',
        ],
    ],
    'errors' => [
        'invalid_input' => 'Phát hiện đầu vào không hợp lệ.',
        'verify_otp_error' => 'Đã xảy ra lỗi khi xác minh mã OTP.',
        'resend_otp_error' => 'Không thể gửi lại mã OTP. Vui lòng thử lại.',
        'invalid_id' => 'ID không hợp lệ',
        'user_not_found' => 'Không tìm thấy người dùng',
        'user_already_verified' => 'Người dùng đã được xác minh',
        'user_already_active' => 'Người dùng đã hoạt động',
        'invalid_access' => 'Truy cập không hợp lệ'
    ],
    'success' => [
        'otp_email' => 'Kiểm tra email của bạn và nhập mã bên dưới',
        'otp_phone' => 'Kiểm tra tin nhắn của bạn và nhập mã bên dưới',
        'otp_resent_email' => 'Mã OTP mới đã được gửi đến email của bạn',
        'otp_resent_phone' => 'Mã OTP mới đã được gửi đến điện thoại của bạn'
    ],
];
