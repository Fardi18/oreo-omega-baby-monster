<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class banner extends Model
{
    use SoftDeletes;
    
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'banners';

    public function __construct()
    {
        parent::__construct();

        $this->setTable(env('PREFIX_TABLE') . $this->table);
    }
}
