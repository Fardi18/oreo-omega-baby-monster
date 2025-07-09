<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class blocked_phone extends Model
{
    use SoftDeletes;

    protected $table = 'blocked_phones';

    protected $dates = [
        'blocked_at'
    ];
}
