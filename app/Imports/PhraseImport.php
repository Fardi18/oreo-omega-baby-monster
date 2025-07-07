<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Illuminate\Support\Facades\DB;

// LIBRARIES
use App\Libraries\Helper;

HeadingRowFormatter::default('none');

// MODELS
use App\Models\phrase;

class PhraseImport implements ToModel, WithHeadingRow
{
    private $module_id = 1;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        DB::beginTransaction();
        try {
            $data = new phrase();
            $data->content = $row['Content'];
            $data->save();

            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();

            $error_msg = $ex->getMessage() . ' in ' . $ex->getFile() . ' at line ' . $ex->getLine();
            Helper::error_logging($error_msg, $this->module_id, null, 'Import App Config');

            if (env('APP_DEBUG') == false) {
                $error_msg = lang('Oops, something went wrong please try again later.', $this->translations);
            }

            # ERROR
            dd($error_msg);
        }
    }

    /**
     * Heading row on different row
     * In case your heading row is not on the first row, you can easily specify this.
     * The 2nd row will now be used as heading row.
     */
    public function headingRow(): int
    {
        return 1;
    }
}
