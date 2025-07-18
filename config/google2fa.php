<?php

return [

    /*
     * Enable / disable Google2FA.
     */
    'enabled' => env('2FA_ENABLED', false),

    /*
     * Lifetime in minutes.
     *
     * In case you need your users to be asked for a new one time passwords from time to time.
     */
    'lifetime' => env('2FA_LIFETIME'), // 0 = eternal

    /*
     * Renew lifetime at every new request.
     */
    'keep_alive' => env('2FA_KEEP_ALIVE'),

    /*
     * Auth container binding.
     */
    'auth' => 'auth',

    /*
     * Guard.
     */
    'guard' => '',

    /*
     * 2FA verified session var.
     */
    'session_var' => 'google2fa',

    /*
     * One Time Password request input name.
     */
    'otp_input' => 'code',

    /*
     * One Time Password Window.
     */
    'window' => 1,

    /*
     * Forbid user to reuse One Time Passwords.
     */
    'forbid_old_passwords' => false,

    /*
     * User's table column for google2fa secret.
     */
    'otp_secret_column' => 'google2fa_secret',

    /*
     * One Time Password View.
     */
    'view' => 'admin.core.login_2fa',

    /*
     * One Time Password error message.
     */
    'error_messages' => [
        'wrong_otp'       => "The 2FA code was wrong.",
        'cannot_be_empty' => '2FA code cannot be empty.',
        'unknown'         => 'An unknown error has occurred. Please try again.',
    ],

    /*
     * Throw exceptions or just fire events?
     */
    'throw_exceptions' => env('OTP_THROW_EXCEPTION', true),

    /*
     * Which image backend to use for generating QR codes?
     *
     * Supports imagemagick, svg and eps
     */
    'qrcode_image_backend' => \PragmaRX\Google2FALaravel\Support\Constants::QRCODE_IMAGE_BACKEND_SVG,

];
