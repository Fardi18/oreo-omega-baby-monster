<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Libraries\Helper;

class HelperController extends Controller
{
    public function access_file($filename)
    {
        $filename_safe = Helper::validate_input_text($filename, true);

        $check_file = \App\Models\file_container::where('unique_id', '=', $filename_safe)
            ->orWhere('unique_file_path', '=', $filename_safe)
            ->first();

        if ($check_file) {
            $path = $check_file->original_file_path;

            if (file_exists($path)) {
                if (env('FILE_PERMISSION_LOG', false)) {
                    $check_file->total_accessed = $check_file->total_accessed + 1;
                    $check_file->save();
                }
                return response()->file($path);
            }
        }

        return response()->json(['error' => 'File not found'], 404);
    }
}
