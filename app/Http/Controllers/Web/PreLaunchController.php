<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Libraries
use App\Libraries\Helper;

// Models
use App\Models\user;
use App\Models\country;
use Illuminate\Support\Facades\Validator;

class PreLaunchController extends Controller
{
    public function register_pre_launch(Request $request){
        $rules = [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'phone_number' => 'nullable|string|max:20|unique:users,phone_number',
            'date_of_birth' => 'nullable|date',
            'market_id' => 'required',
        ];

        $message = [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email has already been taken.',
            'phone_number.unique' => 'Phone number has already been taken.',
            'date_of_birth.date' => 'Date of birth must be a valid date.',
            'market_id.required' => 'Market is required.',
        ];

        $names = [
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'phone_number' => 'Phone Number',
            'date_of_birth' => 'Date of Birth',
            'market_id' => 'Market',  
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
            if(!$first_name) {
                return redirect()
                    ->back()
                    ->withErrors(['first_name' => 'First name is required.'])
                    ->withInput();
            }

            $last_name = Helper::validate_input_text($request->last_name);
            if (!$last_name) {
                return redirect()
                    ->back()
                    ->withErrors(['last_name' => 'Last name is required.'])
                    ->withInput();
            }

            $phone_number = null;
            if ($request->phone_number) {
                $phone_number = Helper::validate_input_text($request->phone_number);
                if (!$phone_number) {
                    return redirect()
                        ->back()
                        ->withErrors(['phone_number' => 'Phone number is invalid.'])
                        ->withInput();
                }
            }

            $email = null;
            if ($request->email) {
                $email = Helper::validate_input_text($request->email);
                if (!$email) {
                    return redirect()
                        ->back()
                        ->withErrors(['email' => 'Phone number is invalid.'])
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
                        ->withErrors(['date_of_birth' => 'Date of birth is invalid.'])
                        ->withInput();
                }
            }

            // check if the dob > now
            if($date_of_birth && $date_of_birth > $now) {
                return redirect()
                    ->back()
                    ->withErrors(['date_of_birth' => 'Date of birth must be in the past.'])
                    ->withInput();
            }

            // check if the user age < 18
            if($date_of_birth && $date_of_birth) {
                $age = date_diff(date_create($date_of_birth), date_create($now))->y;
                if ($age < 18) {
                    return redirect()
                        ->back()
                        ->withErrors(['date_of_birth' => 'You must be at least 18 years old to register.'])
                        ->withInput();
                }
            }

            // Check if market exists
            $market_id = (int) $request->market_id;
            $market_alias = null;
            if($market_id) {
                $market = country::where('id', $market_id)->first();
                if(!$market) {
                    return redirect()
                        ->back()
                        ->withErrors(['market_id' => 'Market not Found'])
                        ->withInput();
                }

                $market_alias = $market->country_alias;
            }

            // create new user
            $user = new user();
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->email = $email;
            $user->phone_number = $phone_number;
            $user->date_of_birth = $date_of_birth;
            $user->market_id = $market_id;
            $user->market_alias = $market_alias;
            $user->save();

            DB::commit();

            // SUCCESS
            $market = strtolower($request->segment(1));
            $locale = strtolower(app()->getLocale());

            return redirect()->route('web.home', [
                'market' => $market,
                'lang' => $locale,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            $error_message = $e->getMessage();
            return response()->json([
                'status' => 'error',
                'message' => $error_message,
            ], 500);
        }
    }
}
