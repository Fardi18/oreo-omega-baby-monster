<?php

namespace App\Http\Controllers\Admin\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

use App\Libraries\Helper;

use App\Models\email_template;

class EmailTemplateController extends Controller
{
    // SET THIS MODULE
    private $module = 'Email Template';
    private $module_id = 21;

    // SET THIS OBJECT/ITEM NAME
    private $item = 'email template';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.core.email_template.list');
    }

    /**
     * Get a listing of the resource using DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data(Datatables $datatables, Request $request)
    {
        $query = email_template::whereNotNull('id');

        return $datatables->eloquent($query)
            ->addColumn('action', function ($data) {
                $object_id = $data->id;
                if (env('CRYPTOGRAPHY_MODE', false)) {
                    $object_id = Helper::generate_token($data->id);
                }

                $wording_edit = ucwords(lang('edit', $this->translations));
                $html = '<a href="' . route('admin.email_template.edit', $object_id) . '" class="btn btn-xs btn-primary btn-block" title="' . $wording_edit . '"><i class="fa fa-pencil"></i>&nbsp; ' . $wording_edit . '</a>';

                $wording_delete = ucwords(lang('delete', $this->translations));
                $html .= '<form action="' . route('admin.email_template.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translations, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $object_id . '">
                <button type="submit" class="btn btn-xs btn-danger btn-block" title="' . $wording_delete . '"><i class="fa fa-trash"></i>&nbsp; ' . $wording_delete . '</button></form>';

                return $html;
            })
            ->addColumn('status_label', function ($data) {
                if ($data->is_active != 1) {
                    return '<span class="label label-danger"><i>' . ucwords(lang('inactive', $this->translations)) . '</i></span>';
                }
                return '<span class="label label-success">' . ucwords(lang('active', $this->translations)) . '</span>';
            })
            ->editColumn('updated_at', function ($data) {
                return Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translations), Helper::get_periods($this->translations));
            })
            ->editColumn('created_at', function ($data) {
                return Helper::locale_timestamp($data->created_at);
            })
            // ->addColumn('image_show', function ($data) {
            //     return '<img src="' . asset($data->image) . '" style="max-width:300px;">';
            // })
            ->rawColumns(['action', 'status_label'])
            ->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.core.email_template.form');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Add New');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        // get table name
        $email_templat_table = (new email_template())->getTable();

        // LARAVEL VALIDATION
        $validation = [
            'unique_name' => 'required|unique:' . $email_templat_table . ',unique_name',
            'subject' => 'required:' . $email_templat_table . ',subject',
        ];
        $message = [
            'required' => ':attribute ' . lang('should not be empty', $this->translations),
            'unique' => ':attribute ' . lang('has already been taken, please input another data', $this->translations),
        ];
        $names = [
            'unique_name' => ucwords(lang('name', $this->translations)),
            'subject' => ucwords(lang('name', $this->translations)),
        ];
        $this->validate($request, $validation, $message, $names);

        DB::beginTransaction();
        try {
            // DB PROCESS BELOW

            // SAVE THE DATA
            $data = new email_template();

            // Ambil nilai dari request
            $unique_name = $request->unique_name;
            // Ubah nilai `unique_name` menjadi format slug
            $unique_name = Helper::generate_slug($unique_name);

            $data->unique_name = $unique_name;
            $data->subject = $request->subject;
            $data->cc = $request->cc;
            $data->bcc = $request->bcc;
            $data->reply_to = $request->reply_to;
            $data->email_body = $request->email_body;
            $data->is_active = (int) $request->is_active;

            $data->save();

            // logging
            $log_detail_id = 5; // add new
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $unique_name . '"';
            $value_before = null;
            $value_after = $data;
            $ip_address = $request->ip();
            Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();

            $error_msg = $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine();
            Helper::error_logging($error_msg, $this->module_id);

            if (env('APP_DEBUG') == false) {
                $error_msg = lang('Oops, something went wrong please try again later.', $this->translations);
            }

            # ERROR
            return back()
                ->withInput()
                ->with('error', $error_msg);
        }

        // SET REDIRECT URL
        $redirect_url = 'admin.email_template';
        if ($request->stay_on_page) {
            $redirect_url = 'admin.email_template.create';
        }

        # SUCCESS
        return redirect()
            ->route($redirect_url)
            ->with('success', lang('Successfully added a new #item : #unique_name', $this->translations, ['#item' => $this->item, '#unique_name' => $unique_name]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  id   $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit($id, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        $raw_id = $id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return redirect()
                ->route('admin.email_template')
                ->with('error', lang('Invalid #item ID, please check your link again', $this->translations, ['#item' => $this->item]));
        }

        // GET DATA BY ID
        $data = email_template::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return redirect()
                ->route('admin.email_template')
                ->with('error', lang('#item not found, please check your link again', $this->translations, ['#item' => $this->item]));
        }
        return view('admin.core.email_template.form', compact('data', 'raw_id'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  id   $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Edit');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        $raw_id = $id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($id);
        }

        // GET DATA BY ID
        $data = email_template::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translations, ['#item' => $this->item]));
        }

        // store data before updated
        $value_before = $data->toJson();

        // LARAVEL VALIDATION
        $validation = [
            'unique_name' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('should not be empty', $this->translations),
        ];
        $names = [
            'unique_name' => ucwords(lang('name', $this->translations)),
        ];
        $this->validate($request, $validation, $message, $names);

        DB::beginTransaction();
        try {
            // DB PROCESS BELOW

            // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
            $unique_name = Helper::validate_input_text($request->unique_name);
            if (!$unique_name) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translations, ['#item' => ucwords(lang('unique_name', $this->translations))]));
            }

            $data->unique_name = $unique_name;
            $data->subject = $request->subject;
            $data->cc = $request->cc;
            $data->bcc = $request->bcc;
            $data->reply_to = $request->reply_to;
            $data->email_body = $request->email_body;
            $data->is_active = (int) $request->is_active;
            $data->save();

            // logging
            $value_after = $data->toJson();
            if ($value_before != $value_after) {
                $log_detail_id = 7; // update
                $module_id = $this->module_id;
                $target_id = $data->id;
                $note = '"' . $unique_name . '"';
                $ip_address = $request->ip();
                Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();

            $error_msg = $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine();
            Helper::error_logging($error_msg, $this->module_id, $id);

            if (env('APP_DEBUG') == false) {
                $error_msg = lang('Oops, something went wrong please try again later.', $this->translations);
            }

            # ERROR
            return back()
                ->withInput()
                ->with('error', $error_msg);
        }

        # SUCCESS
        $success_message = lang('Successfully updated #item : #unique_name', $this->translations, ['#item' => $this->item, '#unique_name' => $unique_name]);
        if ($request->stay_on_page) {
            return redirect()
                ->route('admin.email_template.edit', $raw_id)
                ->with('success', $success_message);
        } else {
            return redirect()
                ->route('admin.email_template')
                ->with('success', $success_message);
        }
    }

    public function send_email_test(Request $request)
    {
        $template = email_template::find($request->template_id);
        if (!$template) {
            return back()->with('error', 'Template not found.');
        }

        $to = $request->to;
        $subject = $template->subject;
        $body = $template->email_body;
        $cc = $request->cc;
        $bcc = $request->bcc;
        $reply_to = $request->reply_to;

        // TODO tambahkan code tracker
        // $tracking_url = route('email_tracker.open', ['email' => $to, 'email_template_id' => $template->id]);
        // $body = str_replace('{tracker}', '<img style="display: none;" src="' . $tracking_url . '" width="1" height="1" />', $body);

        // Kirim email menggunakan Email instance dari Symfony
        Mail::send([], [], function ($message) use ($to, $subject, $body, $cc, $bcc, $reply_to) {
            $message->to($to)
                ->subject($subject)
                ->html($body); // Menggunakan metode html() untuk body email

            if ($reply_to) {
                $message->replyTo(explode(',', $reply_to));
            }
            
            if ($cc) {
                $message->cc(explode(',', $cc));
            }

            if ($bcc) {
                $message->bcc(explode(',', $bcc));
            }
        });

        return back()->with('success', 'Test email has been sent.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Delete');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        $raw_id = $request->id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($raw_id);
        }

        // GET DATA BY ID
        $data = email_template::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translations, ['#item' => $this->item]));
        }

        // store data before updated
        $value_before = $data->toJson();

        // DELETE THE DATA
        if ($data->delete()) {
            // logging
            $log_detail_id = 8; // delete
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $data->name . '"';
            $value_after = $data;
            $ip_address = $request->ip();
            Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);

            # SUCCESS
            return redirect()
                ->route('admin.email_template')
                ->with('success', lang('Successfully deleted #item', $this->translations, ['#item' => $this->item]));
        }

        # FAILED
        return back()
            ->with(
                'error',
                lang('Oops, failed to delete #item. Please try again.', $this->translations, ['#item' => $this->item])
            );
    }

    /**
     * Display a listing of the deleted resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted_data()
    {
        $deleted_data = true;

        return view('admin.core.email_template.list', compact('deleted_data'));
    }

    /**
     * Get a listing of the deleted resource using DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_deleted_data(Datatables $datatables, Request $request)
    {
        $query = email_template::onlyTrashed();

        return $datatables->eloquent($query)
            ->addColumn('action', function ($data) {
                $object_id = $data->id;
                if (env('CRYPTOGRAPHY_MODE', false)) {
                    $object_id = Helper::generate_token($data->id);
                }

                $wording_restore = ucwords(lang('restore', $this->translations));
                return '<form action="' . route('admin.email_template.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translations, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $object_id . '">
                <button type="submit" class="btn btn-xs btn-success" title="' . $wording_restore . '"><i class="fa fa-check"></i>&nbsp; ' . $wording_restore . '</button></form>';
            })
            ->editColumn('deleted_at', function ($data) {
                return Helper::time_ago(strtotime($data->deleted_at), lang('ago', $this->translations), Helper::get_periods($this->translations));
            })
            ->editColumn('created_at', function ($data) {
                return Helper::locale_timestamp($data->created_at);
            })
            ->rawColumns(['action'])
            ->toJson();
    }

    /**
     * Restore the specified deleted resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        $raw_id = $request->id;

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::validate_token($raw_id);
        }

        // GET DATA BY ID
        $data = email_template::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translations, ['#item' => $this->item]));
        }

        // store data before updated
        $value_before = $data->toJson();

        // RESTORE THE DATA
        if ($data->restore()) {
            // logging
            $log_detail_id = 9; // restore
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $data->name . '"';
            $value_after = $data->toJson();
            $ip_address = $request->ip();
            Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);

            # SUCCESS
            return redirect()
                ->route('admin.email_template.deleted_data')
                ->with('success', lang('Successfully restored #item', $this->translations, ['#item' => $this->item]));
        }

        # FAILED
        return back()
            ->with(
                'error',
                lang('Oops, failed to restore #item. Please try again.', $this->translations, ['#item' => $this->item])
            );
    }
}
