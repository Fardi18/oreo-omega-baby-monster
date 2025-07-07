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
            $table->enum('login_page_style', ['default', 'animated'])->default('animated')->after('app_skin');
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
            $table->dropColumn('login_page_style');
        });
    }
};
