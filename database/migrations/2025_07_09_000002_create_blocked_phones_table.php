<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockedPhonesTable extends Migration
{
    public function up()
    {
        Schema::create('blocked_phones', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->text('reason')->nullable();
            $table->timestamp('blocked_at');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blocked_phones');
    }
}
