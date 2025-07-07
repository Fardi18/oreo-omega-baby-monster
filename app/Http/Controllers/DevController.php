<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

// MAIL
use App\Mail\MailTester;

// Libraries
use App\Libraries\Helper;
use App\Models\config;
use App\Models\faq;

class DevController extends Controller
{
    public function sandbox()
    {
        // $string = 'sudo123!';
        // echo Helper::hashing_this($string);

        // $data = faq::get();
        // foreach ($data as $item) {
        //     $item->created_at_formatted = $item->created_at->format('Y-m-d H:i:s');
        //     $item->updated_at_formatted = $item->updated_at->format('Y-m-d H:i:s');
        //     unset($item->created_at);
        //     unset($item->updated_at);
        // }
        // dd(json_encode($data));

        dd(request()->ip(), request()->header('User-Agent'));
    }

    public function cheatsheet_form()
    {
        return view('admin.core.dev.cheatsheet_form');
    }

    public function custom_pages($name)
    {
        $preview = true;
        if ($name == 'login') {
            return view('admin.core.login', compact('preview'));
        } else {
            return view('errors.' . $name, compact('preview'));
        }
    }

    public function encrypt(Request $request)
    {
        if ($request->isMethod('post')) {
            $dir_path = 'uploads/tmp/';
            $file = $request->file('key');
            $uploaded_file = Helper::upload_file($dir_path, $file, true, null, ['txt']);
            if ($uploaded_file['status'] == 'false') {
                return back()
                    ->withInput()
                    ->with('error', $uploaded_file['message']);
            }
            $uploaded_file_name = $uploaded_file['data'];

            $result = Helper::encrypt($request->string, 'public/' . $dir_path . $uploaded_file_name);

            // remove the uploaded key file
            $uploaded_file_path = public_path($dir_path . $uploaded_file_name);
            if (file_exists($uploaded_file_path)) {
                unlink($uploaded_file_path);
            }

            $data = new \stdClass();
            $data->string = $request->string;
            $data->result = $result;

            return view('admin.core.dev.encrypt', compact('data'));
        } else {
            return view('admin.core.dev.encrypt');
        }
    }

    public function decrypt(Request $request)
    {
        if ($request->isMethod('post')) {
            $dir_path = 'uploads/tmp/';
            $file = $request->file('key');
            $uploaded_file = Helper::upload_file($dir_path, $file, true, null, ['txt']);
            if ($uploaded_file['status'] == 'false') {
                return back()
                    ->withInput()
                    ->with('error', $uploaded_file['message']);
            }
            $uploaded_file_name = $uploaded_file['data'];

            $result = Helper::decrypt($request->string, 'public/' . $dir_path . $uploaded_file_name);

            // remove the uploaded key file
            $uploaded_file_path = public_path($dir_path . $uploaded_file_name);
            if (file_exists($uploaded_file_path)) {
                unlink($uploaded_file_path);
            }

            $data = new \stdClass();
            $data->string = $request->string;
            $data->result = $result;

            return view('admin.core.dev.decrypt', compact('data'));
        } else {
            return view('admin.core.dev.decrypt');
        }
    }

    /**
     * EMAIL
     */
    public function email_send(Request $request)
    {
        // SET THE DATA
        $data = \App\Models\admin::first();

        // SET EMAIL SUBJECT
        $subject_email = 'Test Send Email from ' . env('APP_NAME');

        $email_address = $request->email;
        if ($request->send && !$email_address) {
            return 'Must set email as recipient in param email';
        }

        try {
            // SEND EMAIL
            if ($request->send) {
                $arr_email_address = explode(';', $email_address);
                // send email using SMTP
                // Mail::to($arr_email_address)
                //     ->replyTo(['address' => 'sales.kinidishop@gmail.com', 'name' => 'Sales Admin'])
                //     ->cc(['vicky@kiniditech.com', 'vickybudiman25@gmail.com'])
                //     ->bcc(['vicky.subscriber@gmail.com', 'vicky.kinidishop@gmail.com'])
                //     ->send(new MailTester($data, $subject_email));

                $email_data['data'] = [
                    'subject' => $subject_email,
                    'username' => $data->username,
                    'action_url' => url('/')
                ];
                Mail::send('emails.tester_dev', $email_data, function ($message) use ($arr_email_address, $subject_email) {
                    $message->subject($subject_email);
                    $message->to($arr_email_address);
                    $message->replyTo('sales.kinidishop@gmail.com');
                });
            } else {
                // rendering email in browser
                return (new MailTester($data, $subject_email))->render();
            }
        } catch (\Exception $e) {
            // Debug via $e->getMessage();
            dd($e->getMessage());
            // return "We've got errors!";
        }

        return 'Successfully sent email to ' . implode(", ", $arr_email_address);
    }

    public function email_template(Request $request)
    {
        $data = config::first();

        if ($request->isMethod('post')) {

            if ($request->action == 'send') {
                // set email content
                $email_content = $data->email_test_template;
                $email_data['data']['content'] = $email_content;

                // set recipients
                $email_subject = $data->email_test_subject;
                $email_to = $request->email_to;
                $email_reply_to = $request->email_reply_to;
                $email_cc = $request->email_cc;
                $email_bcc = $request->email_bcc;

                // send email
                Mail::send('emails.custom_template', $email_data, function ($message) use ($email_to, $email_subject, $email_reply_to, $email_cc, $email_bcc) {
                    $message->to($email_to)->subject($email_subject);

                    if ($email_reply_to) {
                        $message->replyTo(explode(',', $email_reply_to));
                    }

                    if ($email_cc) {
                        $message->cc(explode(',', $email_cc));
                    }

                    if ($email_bcc) {
                        $message->bcc(explode(',', $email_bcc));
                    }

                    $headers = $message->getHeaders();
                    $headers->addTextHeader('X-MC-PreserveRecipients', true);
                });

                return redirect()
                    ->route('dev.email_template')
                    ->with('success', lang('Successfully sent #item', $this->translations, ['#item' => 'email template']));
            }

            $data->email_test_subject = $request->email_test_subject;
            $data->email_test_template = $request->email_test_template;
            $data->save();

            return redirect()
                ->route('dev.email_template')
                ->with('success', lang('Successfully updated #item', $this->translations, ['#item' => 'email template']));
        } else {
            return view('admin.core.dev.email_template', compact('data'));
        }
    }

    public function nav_menu_structure()
    {
        $json = file_get_contents(public_path('admin/json/nav_menu_structure.json'));
        $data = json_decode($json);
        dd($data);
    }

    public function tester_form(Request $request)
    {
        if ($request->isMethod('post')) {
            # POST request
            dd($request->all());
        } else {
            # GET request
            return view('admin.tester');
        }
    }
}
