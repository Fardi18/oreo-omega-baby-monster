<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(env('PREFIX_TABLE') . 'logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->index('FK_admins');
            $table->foreignId('log_detail_id')->index('FK_log_details');
            $table->foreignId('module_id')->index('FK_modules')->nullable();
            $table->foreignId('target_id')->index('FK_target')->nullable();
            $table->string('note')->nullable();
            $table->text('value_before')->nullable()->comment('JSON format');
            $table->text('value_after')->nullable()->comment('JSON format');
            $table->text('url')->nullable();
            $table->string('ip_address', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(env('PREFIX_TABLE') . 'logs');
    }
}