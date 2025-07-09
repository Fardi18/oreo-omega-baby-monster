<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class blocked_email extends Model
{
    use SoftDeletes;

    protected $table = 'blocked_emails';
    
    protected $dates = [
        'blocked_at'
    ];
}
