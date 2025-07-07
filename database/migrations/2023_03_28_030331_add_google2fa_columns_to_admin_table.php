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
        Schema::table(env('PREFIX_TABLE') . 'admins', function (Blueprint $table) {
            $table->string('google2fa_secret')->nullable()->after('avatar_path');
            $table->boolean('google2fa_enabled')->default(false)->after('google2fa_secret');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(env('PREFIX_TABLE') . 'admins', function (Blueprint $table) {
            $table->dropColumn('google2fa_secret');
            $table->dropColumn('google2fa_enabled');
        });
    }
};
