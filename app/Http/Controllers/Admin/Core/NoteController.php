<?php

namespace App\Http\Controllers\Admin\Core;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\Datatables\Datatables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// Libraries
use App\Libraries\Helper;

// Models
use App\Models\note;

class NoteController extends Controller
{
    // SET THIS MODULE
    private $module = 'Note';
    private $module_id = 18;

    // SET THIS OBJECT/ITEM NAME
    private $item = 'note';

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

        $query = note::whereNotNull('id')
            ->orderBy('fav_status', 'desc')
            ->orderBy('updated_at', 'desc')
            ->limit(11);

        if ($request->q) {
            $keyword = $request->q;
            $query->where(function ($query_where) use ($keyword) {
                $query_where->where('title', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('tags', 'LIKE', '%' . $keyword . '%');
            });
        }

        $data = $query->get();

        return view('admin.core.note.list', compact('data'));
    }

    public function index_v1()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        return view('admin.core.note.list_v1');
    }

    /**
     * Get a listing of the resource using DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data(Datatables $datatables, Request $request)
    {
        $query = note::whereNotNull('id');

        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View All');
        if ($authorize['status'] != 'true') {
            $query->where('author', Helper::get_admin_session()->id);
        }

        return $datatables->eloquent($query)
            ->addColumn('action', function ($data) {
                $object_id = $data->id;
                if (env('CRYPTOGRAPHY_MODE', false)) {
                    $object_id = Helper::generate_token($data->id);
                }

                $wording_edit = ucwords(lang('edit', $this->translations));
                $html = '<a href="' . route('admin.note.edit', $object_id) . '" class="btn btn-xs btn-primary" title="' . $wording_edit . '"><i class="fa fa-pencil"></i>&nbsp; ' . $wording_edit . '</a>';

                $wording_delete = ucwords(lang('delete', $this->translations));
                $html .= '<form action="' . route('admin.note.delete') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to delete this #item?', $this->translations, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $object_id . '">
                <button type="submit" class="btn btn-xs btn-danger" title="' . $wording_delete . '"><i class="fa fa-trash"></i>&nbsp; ' . $wording_delete . '</button></form>';

                return $html;
            })
            ->editColumn('updated_at', function ($data) {
                return Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translations), Helper::get_periods($this->translations));
            })
            ->editColumn('created_at', function ($data) {
                return Helper::locale_timestamp($data->created_at);
            })
            ->rawColumns(['action'])
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

        return view('admin.core.note.form');
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

        // LARAVEL VALIDATION
        $validation = [
            'title' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('should not be empty', $this->translations)
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translations))
        ];
        $this->validate($request, $validation, $message, $names);

        DB::beginTransaction();
        try {
            // DB PROCESS BELOW

            $data = new note();

            // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
            $title = Helper::validate_input_text($request->title);
            if (!$title) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translations, ['#item' => $names['title']]));
            }
            $data->title = $title;

            if ($request->content) {
                $data->content = Helper::generate_token($request->content, 25);
            }

            $tags = Helper::validate_input_text($request->tags);
            if (!$tags) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translations, ['#item' => 'tags']));
            }
            $data->tags = $tags;

            $data->fav_status = (int) $request->fav_status;
            $data->author = (int) Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->id;
            $data->save();

            $item_name = $title;

            // logging
            $log_detail_id = 5; // add new
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $item_name . '"';
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
        $redirect_url = 'admin.note';
        if ($request->stay_on_page) {
            $redirect_url = 'admin.note.create';
        }

        # SUCCESS
        return redirect()
            ->route($redirect_url)
            ->with('success', lang('Successfully added a new #item : #name', $this->translations, ['#item' => $this->item, '#name' => $item_name]));
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
                ->route('admin.note')
                ->with('error', lang('Invalid #item ID, please check your link again', $this->translations, ['#item' => $this->item]));
        }

        // GET DATA BY ID
        $data = note::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return redirect()
                ->route('admin.note')
                ->with('error', lang('#item not found, please check your link again', $this->translations, ['#item' => $this->item]));
        }

        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View All');
        if ($authorize['status'] != 'true' && $data->author != Helper::get_admin_session()->id) {
            return redirect()
                ->route('admin.note')
                ->with('error', $authorize['message']);
        }

        $data->content = Helper::validate_token($data->content, 25);

        return view('admin.core.note.form', compact('data', 'raw_id'));
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
        $data = note::find($id);

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
            'title' => 'required'
        ];
        $message = [
            'required' => ':attribute ' . lang('should not be empty', $this->translations)
        ];
        $names = [
            'title' => ucwords(lang('title', $this->translations))
        ];
        $this->validate($request, $validation, $message, $names);

        DB::beginTransaction();
        try {
            // DB PROCESS BELOW

            // HELPER VALIDATION FOR PREVENT SQL INJECTION & XSS ATTACK
            $title = Helper::validate_input_text($request->title);
            if (!$title) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translations, ['#item' => $names['title']]));
            }
            $data->title = $title;

            if ($request->content) {
                $data->content = Helper::generate_token($request->content, 25);
            }

            $tags = Helper::validate_input_text($request->tags);
            if (!$tags) {
                return back()
                    ->withInput()
                    ->with('error', lang('Invalid format for #item', $this->translations, ['#item' => 'tags']));
            }
            $data->tags = $tags;

            $data->fav_status = (int) $request->fav_status;

            $data->save();

            $item_name = $title;

            // logging
            $value_after = $data->toJson();
            if ($value_before != $value_after) {
                $log_detail_id = 7; // update
                $module_id = $this->module_id;
                $target_id = $data->id;
                $note = '"' . $item_name . '"';
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
        $success_message = lang('Successfully updated #item : #name', $this->translations, ['#item' => $this->item, '#name' => $item_name]);
        if ($request->stay_on_page) {
            return redirect()
                ->route('admin.note.edit', $raw_id)
                ->with('success', $success_message);
        } else {
            return redirect()
                ->route('admin.note')
                ->with('success', $success_message);
        }
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
        $data = note::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translations, ['#item' => $this->item]));
        }

        // store data before updated
        $value_before = $data->toJson();

        $item_name = $data->title;

        // DELETE THE DATA
        if ($data->delete()) {
            // logging
            $log_detail_id = 8; // delete
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $item_name . '"';
            $value_after = $data;
            $ip_address = $request->ip();
            Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);

            # SUCCESS
            return redirect()
                ->route('admin.note')
                ->with('success', lang('Successfully deleted #item', $this->translations, ['#item' => $this->item]));
        }

        # FAILED
        return back()
            ->with('error', lang('Oops, failed to delete #item. Please try again.', $this->translations, ['#item' => $this->item]));
    }

    /**
     * Display a listing of the deleted resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function deleted_data()
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'Restore');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        $deleted_data = true;

        return view('admin.core.note.list', compact('deleted_data'));
    }

    /**
     * Get a listing of the deleted resource using DataTables.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_deleted_data(Datatables $datatables, Request $request)
    {
        $query = note::onlyTrashed();

        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View All');
        if ($authorize['status'] != 'true') {
            $query->where('author', Helper::get_admin_session()->id);
        }

        return $datatables->eloquent($query)
            ->addColumn('action', function ($data) {
                $object_id = $data->id;
                if (env('CRYPTOGRAPHY_MODE', false)) {
                    $object_id = Helper::generate_token($data->id);
                }

                $wording_restore = ucwords(lang('restore', $this->translations));
                return '<form action="' . route('admin.note.restore') . '" method="POST" onsubmit="return confirm(\'' . lang('Are you sure to restore this #item?', $this->translations, ['#item' => $this->item]) . '\');" style="display: inline"> ' . csrf_field() . ' <input type="hidden" name="id" value="' . $object_id . '">
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
        $data = note::onlyTrashed()->find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return back()
                ->withInput()
                ->with('error', lang('#item not found, please reload your page before resubmit', $this->translations, ['#item' => $this->item]));
        }

        // store data before updated
        $value_before = $data->toJson();

        $item_name = $data->title;

        // RESTORE THE DATA
        if ($data->restore()) {
            // logging
            $log_detail_id = 9; // restore
            $module_id = $this->module_id;
            $target_id = $data->id;
            $note = '"' . $item_name . '"';
            $value_after = $data->toJson();
            $ip_address = $request->ip();
            Helper::logging($log_detail_id, $module_id, $target_id, $note, $value_before, $value_after, $ip_address);

            # SUCCESS
            return redirect()
                ->route('admin.note.deleted_data')
                ->with('success', lang('Successfully restored #item', $this->translations, ['#item' => $this->item]));
        }

        # FAILED
        return back()
            ->with('error', lang('Oops, failed to restore #item. Please try again.', $this->translations, ['#item' => $this->item]));
    }

    public function get_data_single($id)
    {
        // AUTHORIZING...
        $authorize = Helper::authorizing($this->module, 'View Details');
        if ($authorize['status'] != 'true') {
            return response()->json([
                'status' => 'false',
                'message' => $authorize['message'],
                'data' => ''
            ]);
        }

        // SET THIS OBJECT/ITEM NAME BASED ON TRANSLATION
        $this->item = ucwords(lang($this->item, $this->translations));

        // CHECK OBJECT ID
        if ((int) $id < 1) {
            // INVALID OBJECT ID
            return response()->json([
                'status' => 'false',
                'message' => lang('Invalid #item ID, please check your link again', $this->translations, ['#item' => $this->item]),
                'data' => ''
            ]);
        }

        // GET DATA BY ID
        $data = note::find($id);

        // CHECK IS DATA FOUND
        if (!$data) {
            # FAILED - DATA NOT FOUND
            return response()->json([
                'status' => 'false',
                'message' => lang('#item not found, please check your link again', $this->translations, ['#item' => $this->item]),
                'data' => ''
            ]);
        }

        // MANIPULATE THE DATA
        $data->content = Helper::validate_token($data->content, 25);
        $data->tags = str_replace(',', ', ', $data->tags);
        $data->updated_at_label = lang('updated', $this->translations) . ': ' . Helper::time_ago(strtotime($data->updated_at), lang('ago', $this->translations), Helper::get_periods($this->translations));

        if (env('CRYPTOGRAPHY_MODE', false)) {
            $id = Helper::generate_token($id);
        }

        # SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get single data',
            'data' => $data,
            'id' => $id
        ];
        return response()->json($response, 200);
    }

    public function load_more(Request $request)
    {
        $limit = (int) $request->limit;
        $offset = (int) $request->offset;

        $data = note::whereNotNull('id')
            ->orderBy('fav_status', 'desc')
            ->orderBy('updated_at', 'desc')
            ->skip($offset)
            ->limit($limit)
            ->get();

        if (isset($data[0])) {
            foreach ($data as $item) {
                $item->last_update = Helper::time_ago(strtotime($item->updated_at), lang('ago', $this->translations), Helper::get_periods($this->translations));
                $item->tags_label = str_replace(',', ', ', $item->tags);
            }
        }

        # SUCCESS
        $response = [
            'status' => 'true',
            'message' => 'Successfully get more data',
            'total' => count($data),
            'data' => $data,
        ];
        return response()->json($response, 200);
    }
}
