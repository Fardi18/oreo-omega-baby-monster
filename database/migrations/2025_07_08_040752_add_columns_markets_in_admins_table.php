<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table(env('PREFIX_TABLE') . 'admins', function (Blueprint $table) {
            $table->boolean('is_need_market')->after('status')->default(0)->index();
            $table->foreignId('market_id')->nullable()->after('is_need_market')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table(env('PREFIX_TABLE') . 'admins', function (Blueprint $table) {
            //
        });
    }
};
