<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(env('PREFIX_TABLE') . 'configs', function (Blueprint $table) {
            $table->enum('app_logo_style', ['logo with app name', 'logo only'])->default('logo with app name')->after('app_logo');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(env('PREFIX_TABLE') . 'configs', function (Blueprint $table) {
            $table->dropColumn('app_logo_style');
        });
    }
};
