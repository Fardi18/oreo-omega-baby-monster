<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class user_otp extends Model
{
    use SoftDeletes;
    
    protected $table = "user_otps";
}
