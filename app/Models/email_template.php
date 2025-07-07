<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class email_template extends Model
{
    use SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'email_templates';

    public function __construct()
    {
        parent::__construct();

        $this->setTable(env('PREFIX_TABLE') . $this->table);
    }
}
