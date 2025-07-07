<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;

// Models
use App\Models\admin_group;

class Helper extends TheHelper
{
    public static function authorizing($module_name, $rule_name)
    {
        // special access for "*root" as Super Administrator group
        $session_group = Session::get('sysadmin_group');
        if ($session_group) {
            foreach ($session_group as $group) {
                if ($group['group_name'] == '*root') {
                    return ['status' => 'true'];
                }
            }
        }

        if (empty($module_name) || empty($rule_name)) {
            return ['status' => 'false', 'message' => 'Sorry, you are unauthorized'];
        }

        // get access from session
        $access = Session::get('sysadmin_access');

        $granted = false;
        foreach ($access as $item) {
            if ($item->module_name == $module_name && $item->rule_name == $rule_name) {
                $granted = true;
                break;
            }
        }

        if ($granted) {
            return ['status' => 'true'];
        }

        // UNAUTHORIZED...
        return ['status' => 'false', 'message' => 'Sorry, you are unauthorized for ' . $rule_name . ' in ' . $module_name . ' module'];
    }

    public static function logging($log_detail_id, $module_id = null, $target_id = null, $note = null, $value_before = null, $value_after = null, $ip_address = null)
    {
        if (env('SYSTEM_LOG', false) == false) {
            return true;
        }

        DB::beginTransaction();
        try {
            // DB PROCESS BELOW

            $log = new \App\Models\log();
            $log->admin_id = Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->id;
            $log->log_detail_id = $log_detail_id;
            $log->module_id = $module_id;
            $log->target_id = $target_id;
            $log->note = $note;
            $log->value_before = $value_before;
            $log->value_after = $value_after;
            $log->url = url()->full();
            $log->ip_address = request()->ip();
            $log->user_agent = request()->userAgent();
            $log->save();

            if (env('SYSTEM_LOG_FILE')) {
                $log_file = 'system_logs.txt';
                $is_log_file_exists = \Illuminate\Support\Facades\Storage::exists($log_file);
                if (!$is_log_file_exists) {
                    \Illuminate\Support\Facades\Storage::disk('local')->put($log_file, '');
                }
                $file_content_raw = \Illuminate\Support\Facades\Storage::get($log_file);
                if (!empty($file_content_raw)) {
                    $file_content = json_decode($file_content_raw);
                }
                $file_content[] = $log;
                $file_content_json = json_encode($file_content);
                \Illuminate\Support\Facades\Storage::disk('local')->put($log_file, $file_content_json);
            }

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();

            $error_msg = $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine();
            Helper::error_logging($error_msg, null, null, 'Failed to System logging');

            dd('SYSTEM LOGGING ERROR: ' . $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine());
        }
    }

    public static function error_logging($error_msg, $module_id = null, $target_id = null, $remarks = null)
    {
        if (env('SYSTEM_ERROR_LOG', false) == false) {
            return true;
        }

        DB::beginTransaction();
        try {
            $data = new \App\Models\error_log();
            $data->url_get_error = url()->full();
            $data->url_prev = url()->previous();
            $data->err_message = $error_msg;
            if (Session::has(env('SESSION_ADMIN_NAME', 'sysadmin'))) {
                $data->admin_id = Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->id;
            }
            $data->module_id = $module_id;
            $data->target_id = $target_id;
            $data->remarks = $remarks;
            $data->ip_address = request()->ip();
            $data->user_agent = request()->userAgent();
            $data->save();

            DB::commit();

            if (env('SYSTEM_ERROR_LOG_FILE')) {
                $log_file = 'system_error_logs.txt';
                $is_log_file_exists = \Illuminate\Support\Facades\Storage::exists($log_file);
                if (!$is_log_file_exists) {
                    \Illuminate\Support\Facades\Storage::disk('local')->put($log_file, '');
                }
                $file_content_raw = \Illuminate\Support\Facades\Storage::get($log_file);
                if (!empty($file_content_raw)) {
                    $file_content = json_decode($file_content_raw);
                }
                $file_content[] = $data;
                $file_content_json = json_encode($file_content);
                \Illuminate\Support\Facades\Storage::disk('local')->put($log_file, $file_content_json);
            }
        } catch (\Exception $ex) {
            DB::rollback();

            dd('FAILED TO ERROR LOGGING: ' . $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine());
        }
    }

    public static function get_periods($translations)
    {
        return array(lang('second', $translations), lang('minute', $translations), lang('hour', $translations), lang('day', $translations), lang('week', $translations), lang('month', $translations), lang('year', $translations), lang('decade', $translations));
    }

    public static function upload_image($dir_path, $image_file, $reformat_image_name = true, $format_image_name = null, $allowed_extensions = null, $generate_thumbnail = false, $thumbnail_width = 0, $thumbnail_height = 0, $thumbnail_quality_percentage = 100, $remove_exif = false, $resize = false, $resize_width = 800, $resize_height = 800, $resize_quality_percentage = 100)
    {
        // SET ALLOWED EXTENSIONS DEFAULT
        if (!$allowed_extensions) {
            $allowed_extensions = ['jpeg', 'jpg', 'png', 'gif'];
        }

        // PROCESSING IMAGE
        $destination_path = public_path($dir_path);
        $image = $image_file;
        $extension = strtolower($image->getClientOriginalExtension());

        // VALIDATING FOR ALLOWED EXTENSIONS
        if (!in_array($extension, $allowed_extensions)) {
            // FAILED
            return [
                'status' => 'false',
                'message' => 'Failed to upload image, please upload image with extensions allowed #item',
                'dynamic_objects' => ['#item' => '(' . implode("/", $allowed_extensions) . ')']
            ];
        }

        // SET IMAGE FILE NAME
        if ($reformat_image_name) {
            // REFORMAT IMAGE NAME USING $format_image_name
            if ($format_image_name) {
                $image_name = $format_image_name;
            } else {
                // REFORMAT IMAGE NAME USING TIMESTAMP
                $image_name = time();
            }
        } else {
            // USING ORIGINAL FILENAME
            $image_name = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        }

        if ($resize) {
            // Buat instance dari gambar
            $img = Image::make($image->getRealPath());

            // Kompres gambar
            $img->resize($resize_width, $resize_height, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destination_path . $image_name . '.' . $extension, $resize_quality_percentage);
        } else {
            // UPLOADING...
            if (!$image->move($destination_path, $image_name . '.' . $extension)) {
                // FAILED
                return [
                    'status' => 'false',
                    'message' => 'Oops, failed to upload image. Please try again or try uploading another one.',
                    'dynamic_objects' => []
                ];
            }
        }

        // GET THE UPLOADED IMAGE RESULT
        $uploaded_image     = $dir_path . $image_name . '.' . $extension;
        $uploaded_image_tmp = $dir_path . $image_name . '-test.' . $extension;

        // REMOVING EXIF DATA
        if ($remove_exif) {
            $image_type = @exif_imagetype($uploaded_image);

            if (in_array($image_type, [2, 7, 8])) { // JPEG, TIFF_II, TIFF_MM
                // Open the input file for binary reading
                $f1 = fopen(public_path($uploaded_image), 'rb');
                // Open the output file for binary writing
                $f2 = fopen(public_path($uploaded_image_tmp), 'wb');

                // Find EXIF marker
                while (($s = fread($f1, 2))) {
                    $word = unpack('ni', $s)['i'];
                    if ($word == 0xFFE1) {
                        // Read length (includes the word used for the length)
                        $s = fread($f1, 2);
                        $len = unpack('ni', $s)['i'];
                        // Skip the EXIF info
                        fread($f1, $len - 2);
                        break;
                    } else {
                        fwrite($f2, $s, 2);
                    }
                }

                // Write the rest of the file
                while (($s = fread($f1, 4096))) {
                    fwrite($f2, $s, strlen($s));
                }

                fclose($f1);
                fclose($f2);

                copy($uploaded_image_tmp, $uploaded_image);
                unlink($uploaded_image_tmp);

                $orientation = null;
                $exif = exif_read_data($uploaded_image);
                // RESTORE IMAGE ORIENTATION
                if (isset($exif['Orientation'])) {
                    $orientation = $exif['Orientation'];

                    $imageResource = imagecreatefromjpeg($uploaded_image);
                    switch ($orientation) {
                        case 3:
                            $image = imagerotate($imageResource, 180, 0);
                            break;
                        case 6:
                            $image = imagerotate($imageResource, -90, 0);
                            break;
                        case 8:
                            $image = imagerotate($imageResource, 90, 0);
                            break;
                    }

                    imagejpeg($image, $uploaded_image);
                    imagedestroy($imageResource);
                    imagedestroy($image);
                }
            }
        }

        // GENERATE IMAGE THUMBNAIL - http://image.intervention.io/api/make
        $thumbnail_name = null;
        // VALIDATE THUMBNAIL SIZE
        $thumbnail_width = (int) $thumbnail_width;
        $thumbnail_height = (int) $thumbnail_height;
        if ($generate_thumbnail && $thumbnail_width && $thumbnail_height && $extension != 'gif') {
            // VALIDATE THUMBNAIL QUALITY PERCENTAGE
            if ($thumbnail_quality_percentage > 100) {
                $thumbnail_quality_percentage = 100;
            } else if ($thumbnail_quality_percentage < 1) {
                $thumbnail_quality_percentage = 50;
            }
            // SET THUMBNAIL FILENAME
            $thumbnail_name = $image_name . '-' . $thumbnail_width . 'x' . $thumbnail_height . '.' . $extension;
            try {
                // CREATE A NEW IMAGE FROM GD RESOURCE
                switch ($extension) {
                    case 'jpg':
                        $image_source = imagecreatefromjpeg(public_path($uploaded_image));
                        break;
                    case 'jpeg':
                        $image_source = imagecreatefromjpeg(public_path($uploaded_image));
                        break;
                    case 'png':
                        $image_source = imagecreatefrompng(public_path($uploaded_image));
                        break;
                    case 'gif':
                        $image_source = imagecreatefromgif(public_path($uploaded_image));
                        break;
                    default:
                        // FAILED
                        return [
                            'status' => 'false',
                            'data' => $image_name,
                            'message' => 'Successfully uploaded the image, but failed to generate thumbnail as supported formats are only #item',
                            'dynamic_objects' => ['#item' => 'jpeg/jpg/png/gif']
                        ];
                }
                // OPEN FILE A IMAGE RESOURCE
                $img_thumb = Image::make($image_source);
                // CROP THEN RESIZE TO AxB PIXEL
                $img_thumb->fit($thumbnail_width, $thumbnail_height);
                // SAVE CROPPED FILE WITH X% QUALITY
                $img_thumb->save($dir_path . $thumbnail_name, $thumbnail_quality_percentage);
                // THUMBNAIL IMAGE GENERATED SUCCESSFULLY
            } catch (\Intervention\Image\Exception\NotReadableException $e) {
                // THROWING ERROR WHEN EXCEPTION OCCURS

                Helper::error_logging($e, null, null, 'Failed to upload image');

                // FAILED
                return [
                    'status' => 'false',
                    'message' => $e,
                    'dynamic_objects' => []
                ];
            }
        }

        if ($extension == 'gif') {
            $thumbnail_name = $image_name . '.' . $extension;
        }

        // SUCCESS
        return [
            'status' => 'true',
            'message' => 'Successfully uploaded the image',
            'data' => $image_name  . '.' . $extension,
            'thumbnail' => $thumbnail_name
        ];
    }

    public static function check_slug($table_name, $slug, $field_name = 'slug')
    {
        $unique = false;
        $no = 2;
        $slug_raw = $slug;
        while (!$unique) {
            $slug_exist = DB::table($table_name)->where($field_name, $slug)->count();
            if ($slug_exist == 0) {
                $unique = true;
            } else {
                // SET NEW SLUG
                $slug = $slug_raw . '-' . $no;
                $no++;
            }
        }
        return $slug;
    }

    public static function check_unique($table_name, $value, $field_name = 'slug')
    {
        $unique = false;
        $no = 2;
        $value_raw = $value;
        while (!$unique) {
            $value_exist = DB::table($table_name)->where($field_name, $value)->count();
            if ($value_exist == 0) {
                $unique = true;
            } else {
                // SET NEW SLUG
                $value = $value_raw . '-' . $no;
                $no++;
            }
        }
        return $value;
    }

    public static function upload_file(
        $target_path,
        $file,
        $reformat_file_name = true,
        $format_file_name = null,
        $allowed_extensions = [],
        $filesystem_driver = null,
        $file_permission = ''
    ) {
        $file_extension = $file->getClientOriginalExtension();

        // validate allowed extensions 
        if (!empty($allowed_extensions)) {
            if (!in_array(strtolower($file_extension), $allowed_extensions)) {
                // ERROR
                return [
                    "status" => false,
                    "message" => "Failed to upload the file, please upload file with allowed extensions.",
                    "uploaded_path" => "",
                    "uploaded_file" => "",
                    "uploaded_full_path" => "",
                ];
            }
        }

        // reformat file name
        if ($reformat_file_name) {
            // reformat file name using $format_file_name
            if ($format_file_name) {
                $file_name = $format_file_name . '.' . $file_extension;
            } else {
                // reformat file name using random string
                $file_name = md5(uniqid()) . '.' . $file_extension;
            }
        } else {
            // using original filename
            $file_name = $file->getClientOriginalName();
        }

        // upload file
        if (!$filesystem_driver) {
            // get default value from ENV
            $filesystem_driver = env('FILESYSTEM_DRIVER', 'local');
        }

        switch ($filesystem_driver) {
            case 's3':
                # STORE TO AWS S3
                $uploaded_file = AmazonS3::upload($file, true, $file_name, $target_path);
                $uploaded_full_path = $uploaded_file;
                break;

            default:
                # local - STORE TO LOCAL STORAGE
                if (substr($target_path, '-1') != '/') {
                    $target_path .= '/';
                }
                $uploaded_file = $file->move(public_path($target_path), $file_name);
                $uploaded_full_path = ($uploaded_file) ? $target_path . $file_name : "";
                break;
        }

        // return response
        if ($uploaded_file) {
            if ($file_permission) {
                // cek value di ENV
                $file_permission = env('FILE_PERMISSION', false);
            }

            if (empty($file_permission)) {
                // jika "file permission" aktif
                $log = new \App\Models\file_container();
                $log->unique_id = Helper::random_code_unique(25, true, $log->getTable(), 'unique_id');
                $log->file_extension = $file_extension;
                $log->original_file_path = $uploaded_full_path;

                $file_name_only = str_replace('.' . $file_extension, '', $file_name);

                $log->unique_file_path = $unique_file_name = $file_name_only . '-' . Helper::unique_string() . '.' . $file_extension;
                $unique_file_path = 'files/' . $unique_file_name;

                $session_admin = Helper::get_admin_session();
                if ($session_admin) {
                    $log->admin_id = $session_admin->id;
                }
                $log->save();

                // SUCCESS
                return [
                    "status" => true,
                    "message" => "Successfully uploaded the file.",
                    "uploaded_path" => $unique_file_path,
                    "uploaded_file" => $unique_file_path,
                    "uploaded_full_path" => $unique_file_path,
                ];
            }

            // SUCCESS
            return [
                "status" => true,
                "message" => "Successfully uploaded the file.",
                "uploaded_path" => $target_path,
                "uploaded_file" => $file_name,
                "uploaded_full_path" => $uploaded_full_path,
            ];
        }

        // ERROR
        return [
            "status" => false,
            "message" => "Oops, failed to upload file. Please try again or try upload another one.",
            "uploaded_path" => "",
            "uploaded_file" => "",
            "uploaded_full_path" => "",
        ];
    }

    public static function delete_file($filename, $force = false)
    {
        $filename_safe = Helper::validate_input_text($filename, true);
        $filename_safe = str_replace('files/', '', $filename_safe);

        $check_file = \App\Models\file_container::where('unique_id', '=', $filename_safe)
            ->orWhere('unique_file_path', '=', $filename_safe)
            ->first();

        if ($check_file) {
            $path = $check_file->original_file_path;

            if (file_exists($path)) {
                if ($force) {
                    # hard delete
                    $check_file->forceDelete();
                    unlink($path);
                } else {
                    # soft delete
                    $check_file->delete();
                }
                
                return response()->json(['message' => 'Successfully deleted file'], 200);
            }
        }

        return response()->json(['error' => 'File not found'], 404);
    }

    public static function is_menu_active($word_in_url)
    {
        $actual_link = Helper::get_url();
        if (strpos($actual_link, $word_in_url) !== false) {
            // FOUND
            return true;
        }
        return false;
    }

    public static function get_avatar()
    {
        if (Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->avatar_with_path) {
            return asset(Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'))->avatar_with_path);
        } else {
            return asset('images/avatar.png');
        }
    }

    /**
     * Convert timestamp from server timezone to app timezone
     */
    public static function locale_timestamp($timestamp, $format = 'D, d M Y H:i:s', $with_gmt = true)
    {
        $local_timezone = env('APP_TIMEZONE', 'UTC');

        $locale_timestamp = date($format, strtotime($timestamp->setTimezone($local_timezone)));

        if ($with_gmt) {
            $locale_timestamp .= ' (' . $local_timezone . ')';
        }

        return $locale_timestamp;
    }

    /**
     * Convert timestamp from app timezone to server timezone
     */
    public static function server_timestamp($timestamp, $format = 'Y-m-d H:i:s')
    {
        $local_timezone = env('APP_TIMEZONE', 'UTC');
        $date = new \DateTime($timestamp, new \DateTimeZone($local_timezone));
        $date->setTimezone(new \DateTimeZone(date_default_timezone_get()));

        return $date->format($format);
    }

    public static function current_datetime($format = 'Y-m-d H:i:s', $timezone = null)
    {
        if (!$timezone) {
            $timezone = env('APP_TIMEZONE', 'UTC');
        }

        $date = new \DateTime(null, new \DateTimeZone($timezone));
        return $date->format($format);
    }

    public static function loadEncryptionKeyFromConfig($path_key = null)
    {
        if (!$path_key) {
            $path_key = env('PHP_ENCRYPTION_PATH');
        }
        $keyAscii = file_get_contents(base_path($path_key));
        return Key::loadFromAsciiSafeString($keyAscii);
    }

    public static function encrypt($secret_data, $path_key = null)
    {
        $key = Helper::loadEncryptionKeyFromConfig($path_key);
        $ciphertext = Crypto::encrypt($secret_data, $key);
        return $ciphertext;
    }

    public static function decrypt($ciphertext, $path_key = null)
    {
        $key = Helper::loadEncryptionKeyFromConfig($path_key);
        try {
            $secret_data = Crypto::decrypt($ciphertext, $key);
        } catch (\Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
            // An attack! Either the wrong key was loaded, or the ciphertext has
            // changed since it was created -- either corrupted in the database or
            // intentionally modified by someone trying to carry out an attack.

            $error_msg = 'Failed to decrypt the data for some reasons. Maybe the wrong key was loaded, or the ciphertext has changed, or corrupted in the database, or intentionally modified by someone trying to carry out an attack';
            Helper::error_logging($error_msg, null, null, json_encode($ex));

            return $error_msg;
        }

        return $secret_data;
    }

    public static function decrypt_config($ciphertext)
    {
        if (env('SECURE_CONFIG')) {
            return Helper::decrypt($ciphertext);
        }
        return $ciphertext;
    }

    public static function get_admin_session()
    {
        return Session::get(env('SESSION_ADMIN_NAME', 'sysadmin'));
    }

    public static function simple_encode($string)
    {
        return base64_encode(base64_encode(base64_encode($string)));
    }

    public static function simple_decode($string)
    {
        return base64_decode(base64_decode(base64_decode($string)));
    }

    public static function locale_currency_format($nominal)
    {
        return Helper::currency_format($nominal, 0, ',', '.', env('DEFAULT_CURRENCY', 'Rp'), ',-');
    }

    public static function random_code_unique($length = null, $use_only_capital_letters = false, $check_unique_in_table = null, $check_unique_in_field = null)
    {
        if ($check_unique_in_table && $check_unique_in_field) {
            do {
                $code = Helper::random_code($length, $use_only_capital_letters);
                $value_exist = DB::table($check_unique_in_table)->where($check_unique_in_field, $code)->count();
            } while ($value_exist > 0);
        } else {
            $code = Helper::random_code($length, $use_only_capital_letters);
        }

        return $code;
    }

    public static function get_admin_groups()
    {
        $query = admin_group::where('status', 1);

        $authorize = Helper::authorizing('Super User', 'All Access');
        if ($authorize['status'] != 'true') {
            $query->where('name', '!=', '*root');
        }

        $data = $query->orderBy('name')->get();

        return $data;
    }
}
