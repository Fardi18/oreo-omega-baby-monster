<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

// Libraries
use App\Libraries\Helper;

// Models
use App\Models\user;
use App\Models\country;


class PostLaunchController extends Controller
{
    public function register_post_launch_page(Request $request)
    {
        $markets = country::where('status', 1)
            ->orderBy('country_name', 'asc')
            ->get();

        return view('web.post_launch.register', compact('markets'));
    }

    private function hasForbiddenContent($text)
    {
        // List of forbidden words/patterns
        $forbidden = [
            'fuck',
            'shit',
            'admin',
            'root',
            'hack',
            'system',
            'administrator',
            '<script',
            'javascript:',
            'alert(',
            'document.cookie',
            '../',
            '..\\',
            '/etc/',
            'eval('
        ];

        $text = strtolower($text);
        foreach ($forbidden as $word) {
            if (strpos($text, strtolower($word)) !== false) {
                return true;
            }
        }

        // Check for SQL injection attempts
        $sql_patterns = [
            '/union\s+select/i',
            '/select.*from/i',
            '/insert\s+into/i',
            '/delete\s+from/i',
            '/drop\s+table/i',
            '/update.*set/i',
            '/\d+\s*=\s*\d+/'
        ];

        foreach ($sql_patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    private function blockUserContact($email = null, $phone = null)
    {
        DB::beginTransaction();
        try {
            if ($email) {
                $blocked_email = new \App\Models\blocked_email();
                $blocked_email->email = $email;
                $blocked_email->reason = 'Forbidden content in registration';
                $blocked_email->blocked_at = now();
                $blocked_email->save();
            }

            if ($phone) {
                $blocked_phone = new \App\Models\blocked_phone();
                $blocked_phone->phone_number = $phone;
                $blocked_phone->reason = 'Forbidden content in registration';
                $blocked_phone->blocked_at = now();
                $blocked_phone->save();
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    public function register_post_launch_process(Request $request)
    {
        // Check for forbidden content in names
        if ($this->hasForbiddenContent($request->first_name) || $this->hasForbiddenContent($request->last_name)) {
            // Block the email and phone if provided
            if ($request->email || $request->phone_number) {
                $this->blockUserContact($request->email, $request->phone_number);
            }

            return redirect()
                ->back()
                ->withErrors(['error' => __('messages.errors.invalid_input')])
                ->withInput();
        }

        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'market_id' => 'required',
        ];

        // Check for blocked email/phone
        if ($request->email) {
            $blocked_email = \App\Models\blocked_email::where('email', $request->email)->first();
            if ($blocked_email) {
                return redirect()
                    ->back()
                    ->withErrors(['email' => __('messages.validation.email.blocked')])
                    ->withInput();
            }
        }

        if ($request->phone_number) {
            $blocked_phone = \App\Models\blocked_phone::where('phone_number', $request->phone_number)->first();
            if ($blocked_phone) {
                return redirect()
                    ->back()
                    ->withErrors(['phone_number' => __('messages.validation.phone_number.blocked')])
                    ->withInput();
            }
        }

        $message = [
            'first_name.required' => __('messages.validation.first_name.required'),
            'last_name.required' => __('messages.validation.last_name.required'),
            'email.email' => __('messages.validation.email.email'),
            'email.unique' => __('messages.validation.email.unique'),
            'phone_number.unique' => __('messages.validation.phone_number.unique'),
            'date_of_birth.date' => __('messages.validation.date_of_birth.date'),
            'market_id.required' => __('messages.validation.market_id.required'),
        ];

        $names = [
            'first_name' => __('First Name'),
            'last_name' => __('Last Name'),
            'email' => __('Email'),
            'phone_number' => __('Phone Number'),
            'date_of_birth' => __('Date of Birth'),
            'market_id' => __('Market'),
        ];

        $validator = Validator::make($request->all(), $rules, $message, $names);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Laravel validation
            $first_name = Helper::validate_input_text($request->first_name);
            if (!$first_name) {
                return redirect()
                    ->back()
                    ->withErrors(['first_name' => __('messages.validation.first_name.required')])
                    ->withInput();
            }

            $last_name = Helper::validate_input_text($request->last_name);
            if (!$last_name) {
                return redirect()
                    ->back()
                    ->withErrors(['last_name' => __('messages.validation.last_name.required')])
                    ->withInput();
            }

            $phone_number = null;
            if ($request->phone_number) {
                $phone_number = Helper::validate_input_text($request->phone_number);
                if (!$phone_number) {
                    return redirect()
                        ->back()
                        ->withErrors(['phone_number' => __('messages.validation.phone_number.invalid')])
                        ->withInput();
                }
            }

            $email = null;
            if ($request->email) {
                $email = Helper::validate_input_text($request->email);
                if (!$email) {
                    return redirect()
                        ->back()
                        ->withErrors(['email' => __('messages.validation.email.invalid')])
                        ->withInput();
                }
            }

            $now = date('Y-m-d');
            $dob_year = $request->dob_year;
            $dob_month = $request->dob_month;
            $dob_day = $request->dob_day;

            $date_of_birth = null;
            if ($dob_year && $dob_month && $dob_day) {
                $date_of_birth = date('Y-m-d', strtotime($dob_year . '-' . $dob_month . '-' . $dob_day));
                if (!$date_of_birth) {
                    return redirect()
                        ->back()
                        ->withErrors(['date_of_birth' => __('messages.validation.date_of_birth.invalid')])
                        ->withInput();
                }
            }

            // check if the dob > now
            if ($date_of_birth && $date_of_birth > $now) {
                return redirect()
                    ->back()
                    ->withErrors(['date_of_birth' => __('messages.validation.date_of_birth.future')])
                    ->withInput();
            }

            // check if the user age < 18
            if ($date_of_birth && $date_of_birth) {
                $age = date_diff(date_create($date_of_birth), date_create($now))->y;
                if ($age < 18) {
                    return redirect()
                        ->back()
                        ->withErrors(['date_of_birth' => __('messages.validation.date_of_birth.underage')])
                        ->withInput();
                }
            }

            // Check if market exists
            $market_id = (int) $request->market_id;
            $market_alias = null;
            if ($market_id) {
                $market = country::where('id', $market_id)->first();
                if (!$market) {
                    return redirect()
                        ->back()
                        ->withErrors(['market_id' => __('messages.validation.market_id.not_found')])
                        ->withInput();
                }

                $market_alias = $market->country_alias;
            }

            $existingUser = null;
            if ($email) {
                $existingUser = user::where('email', $email)->first();
            }
            if (!$existingUser && $phone_number) {
                $existingUser = user::where('phone_number', $phone_number)->first();
            }

            if ($existingUser) {
                if ($existingUser->market_id != $market_id) {
                    return redirect()
                        ->back()
                        ->withErrors(['email' => __('messages.validation.email.used_in_another_market')])
                        ->withInput();
                }

                // Update existing user
                $existingUser->first_name = $first_name;
                $existingUser->last_name = $last_name;
                $existingUser->date_of_birth = $date_of_birth;
                $existingUser->market_alias = $market_alias;
                $existingUser->type = 'post-launch';
                $existingUser->save();

                $user = $existingUser;
            } else {
                // Create new user
                $user = new user();
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->email = $email;
                $user->phone_number = $phone_number;
                $user->date_of_birth = $date_of_birth;
                $user->market_id = $market_id;
                $user->market_alias = $market_alias;
                $user->type = 'post-launch';
                $user->save();
            }

            // if user successfully created, create the user otp with 4 digits
            if ($user) {
                $otp = new \App\Models\user_otp();
                $otp->user_id = $user->id;
                $otp->otp = rand(1000, 9999);
                if ($user->market_alias == 'KH') {
                    $otp->type = 'phone';
                } else {
                    $otp->type = 'email';
                }
                $otp->expires_at = now()->addMinutes((int) env('OTP_EXPIRATION', 5));
                $otp->save();
            }

            // TODO SEND THE OTP TO PHONE OR EMAIL

            DB::commit();

            // SUCCESS
            $market = strtolower($user->market_alias);
            $locale = strtolower(app()->getLocale());

            $redirect_message = '';
            if ($otp->type == 'phone') {
                $redirect_message = __('messages.success.otp_phone');
            } else {
                $redirect_message = __('messages.success.otp_email');
            }

            // generate the object id for the user
            $object_id = Helper::generate_token($user->id);

            return redirect()
                ->route('web.verify_otp_page', [
                    'id' => $object_id,
                    'market' => $market,
                    'lang' => $locale,
                ])
                ->with('success', $redirect_message);
        } catch (\Exception $e) {
            DB::rollBack();
            $error_message = $e->getMessage();
            return response()->json([
                'status' => 'error',
                'message' => $error_message,
            ], 500);
        }
    }

    public function create_pin_page(Request $request, $market, $lang, $id)
    {
        $raw_id = $id;
        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        $user = user::where('id', $id)->first();
        if (!$user || !$user->is_active || !$user->verified_at) {
            return redirect()->route('web.landing_page', compact('market', 'lang'))
                ->withErrors(['id' => __('messages.errors.invalid_access')]);
        }

        return view('web.post_launch.create_pin', compact('user', 'market', 'lang', 'raw_id'));
    }

    public function create_pin_process(Request $request, $market, $lang, $id)
    {
        $raw_id = $id;
        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        $user = user::where('id', $id)->first();
        if (!$user || !$user->is_active || !$user->verified_at) {
            return redirect()->route('web.landing_page', compact('market', 'lang'))
                ->withErrors(['id' => __('messages.errors.invalid_access')]);
        }

        $rules = [
            'pin' => 'required|digits:4',
        ];

        $message = [
            'pin.required' => __('messages.validation.pin.required'),
            'pin.digits' => __('messages.validation.pin.digits'),
        ];

        $names = [
            'pin' => __('pin'),
        ];

        $validator = Validator::make($request->all(), $rules, $message, $names);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $encrypted_pin = Helper::hashing_this($request->pin);

        $user->pin = $encrypted_pin;
        $user->save();

        return redirect()->route('web.success_page', [
            'market' => $market,
            'lang'   => $lang,
            'id'     => $raw_id
        ])->with('success', __('messages.success.pin_set'));
    }

    public function login_page(Request $request, $market, $lang)
    {
        $markets = country::where('status', 1)
            ->orderBy('country_name', 'asc')
            ->get();

        return view('web.post_launch.login', compact('markets', 'market', 'lang'));
    }

    public function login_process(Request $request, $market, $lang)
    {
        $rules = [
            'email_or_phone_number' => 'required|string|max:255',
            'pin' => 'required|digits:4',
        ];

        $message = [
            'email_or_phone_number.required' => __('messages.validation.email_or_phone_number.required'),
            'email_or_phone_number.string' => __('messages.validation.email_or_phone_number.string'),
            'email_or_phone_number.max' => __('messages.validation.email_or_phone_number.max'),
            'pin.required' => __('messages.validation.pin.required'),
            'pin.digits' => __('messages.validation.pin.digits'),
        ];

        $names = [
            'email_or_phone_number' => __('Email or Phone Number'),
            'pin' => __('PIN'),
        ];

        $validator = Validator::make($request->all(), $rules, $message, $names);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->input('email_or_phone_number');
            if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $user = user::where('email', $input)->first();
            } else {
                $user = user::where('phone_number', $input)->first();
            }

            if (!$user || !$user->is_active || !$user->verified_at) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => __('messages.errors.invalid_access')])
                    ->withInput();
            }

            if (!password_verify($request->pin, $user->pin)) {
                return redirect()
                    ->back()
                    ->withErrors(['pin' => __('messages.validation.pin.invalid')])
                    ->withInput();
            }

            $marketModel = country::find($user->market_id);
            if (!$marketModel) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => __('messages.errors.invalid_market')])
                    ->withInput();
            }

            Session::put(env('SESSION_USER', 'user'), $user);
            Session::put(env('SESSION_MARKET', 'user_market'), strtolower($marketModel->country_alias));
            Session::put(env('SESSION_LANGUAGE', 'user_language'), strtolower($lang));

            DB::commit();

            return redirect()->route('web.welcome', [
                'market' => strtolower($marketModel->country_alias),
                'lang' => strtolower($lang)
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Simpan log jika perlu: Log::error($e)
            return redirect()
                ->back()
                ->withErrors(['error' => __('messages.errors.general')])
                ->withInput();
        }
    }

    public function logout(Request $request, $market, $lang)
    {
        // Clear session data
        Session::forget(env('SESSION_USER', 'user'));
        Session::forget(env('SESSION_MARKET', 'user_market'));
        Session::forget(env('SESSION_LANGUAGE', 'user_language'));

        return redirect()->route('web.login_page', [
            'market' => strtolower($market),
            'lang' => strtolower($lang)
        ])->with('success', __('messages.success.logout'));
    }

    public function forgot_pin_page(Request $request, $market, $lang)
    {
        $markets = country::where('status', 1)
            ->orderBy('country_name', 'asc')
            ->get();

        return view('web.post_launch.forgot_pin', compact('markets', 'market', 'lang'));
    }

    public function forgot_pin_process(Request $request, $market, $lang)
    {
        $rules = [
            'email_or_phone_number' => 'required|string|max:255',
        ];

        $message = [
            'email_or_phone_number.required' => __('messages.validation.email_or_phone_number.required'),
            'email_or_phone_number.string' => __('messages.validation.email_or_phone_number.string'),
            'email_or_phone_number.max' => __('messages.validation.email_or_phone_number.max'),
        ];

        $names = [
            'email_or_phone_number' => __('Email or Phone Number'),
            'pin' => __('PIN'),
        ];

        $validator = Validator::make($request->all(), $rules, $message, $names);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            DB::beginTransaction();

            $input = $request->input('email_or_phone_number');
            if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
                $type = 'email';
                $user = user::where('email', $input)->first();
            } else {
                $type = 'phone';
                $user = user::where('phone_number', $input)->first();
            }

            if (!$user || !$user->is_active || !$user->verified_at) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => __('messages.errors.invalid_access')])
                    ->withInput();
            }

            // check if the user has activated their account
            if($user->is_active == 0) {
                return redirect()
                    ->back()
                    ->withErrors(['error' => __('messages.errors.account_not_activated')])
                    ->withInput();
            }

            // generate the new OTP
            $otp = new \App\Models\user_otp();
            $otp->user_id = $user->id;
            $otp->otp = rand(1000, 9999);
            $otp->type = $type;
            $otp->expires_at = now()->addMinutes((int) env('OTP_EXPIRATION', 5));
            $otp->save();

            DB::commit();

            // SUCCESS
            $market = strtolower($user->market_alias);
            $locale = strtolower(app()->getLocale());

            $redirect_message = '';
            if ($otp->type == 'phone') {
                $redirect_message = __('messages.success.otp_phone');
            } else {
                $redirect_message = __('messages.success.otp_email');
            }

            // generate the object id for the user
            $object_id = Helper::generate_token($user->id);

            return redirect()
                ->route('web.forgot_pin_otp_page', [
                    'id' => $object_id,
                    'market' => $market,
                    'lang' => $locale,
                ])
                ->with('success', $redirect_message);
        } catch (\Throwable $e) {
            DB::rollBack();

            // Simpan log jika perlu: Log::error($e)
            return redirect()
                ->back()
                ->withErrors(['error' => __('messages.errors.general')])
                ->withInput();
        }
    }

    public function forgot_pin_otp_page($market, $lang, $id, Request $request)
    {
        $raw_id = $id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        if ((int) $id < 1) {
            return redirect()
                ->route('web.login_page', [
                    'market' => $market,
                    'lang' => $lang,
                ])
                ->withErrors(['id' => __('messages.errors.invalid_id')]);
        }

        // Check if the user exists
        $user = user::where('id', $id)->first();
        if (!$user) {
            return redirect()
                ->route('web.login_page', [
                    'market' => $market,
                    'lang' => $lang,
                ])
                ->withErrors(['id' => __('messages.errors.user_not_found')]);
        }

        return view('web.post_launch.forgot_pin_otp', compact('user', 'market', 'lang', 'raw_id'));
    }

    public function forgot_pin_otp_process($market, $lang, $id, Request $request)
    {
        $raw_id = $id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        if ((int) $id < 1) {
            return redirect()
                ->route('web.web.login_page', [
                    'market' => $market,
                    'lang' => $lang,
                ])
                ->withErrors(['id' => __('messages.errors.invalid_id')]);
        }

        // Check if the user exists
        $user = user::where('id', $id)->first();
        if (!$user) {
            return redirect()
                ->route('web.web.login_page', [
                    'market' => $market,
                    'lang' => $lang,
                ])
                ->withErrors(['id' => __('messages.errors.user_not_found')]);
        }

        $rules = [
            'otp' => 'required|digits:4',
        ];

        $message = [
            'otp.required' => __('messages.validation.otp.required'),
            'otp.digits' => __('messages.validation.otp.digits'),
        ];

        $names = [
            'otp' => __('OTP'),
        ];

        $validator = Validator::make($request->all(), $rules, $message, $names);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Check if the OTP is valid
            $otp = \App\Models\user_otp::where('user_id', $id)
                ->where('otp', $request->otp)
                ->first();

            if (!$otp) {
                return redirect()
                    ->back()
                    ->withErrors(['otp' => __('messages.validation.otp.invalid')])
                    ->withInput();
            }

            if ($otp->expires_at < now()) {
                return redirect()
                    ->back()
                    ->withErrors(['otp' => __('messages.validation.otp.expired')])
                    ->withInput();
            }

            // Mark the user as verified
            $user->verified_at = date('Y-m-d H:i:s');
            $user->is_active = 1;
            $user->save();

            // update the otp data
            if ($otp->type == 'phone') {
                $otp->phone_verified_at = date('Y-m-d H:i:s');
            } else {
                $otp->email_verified_at = date('Y-m-d H:i:s');
            }
            $otp->save();

            return redirect()
                ->route('web.create_pin_page', [
                    'market' => $market,
                    'lang' => $lang,
                    'id' => $raw_id
                ]);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withErrors(['error' => __('messages.errors.verify_otp_error')])
                ->withInput();
        }
    }
}
