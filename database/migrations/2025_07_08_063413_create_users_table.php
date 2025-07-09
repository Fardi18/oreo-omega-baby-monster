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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->index();
            $table->string('market_alias')->index();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable()->unique()->index();
            $table->string('phone_number')->nullable()->unique()->index();
            $table->date('date_of_birth')->nullable();
            $table->string('pin')->nullable()->index();
            $table->enum('type', ['pre-launch', 'post-launch'])->default('pre-launch')->index();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(false)->index();
            $table->dateTime('verified_at')->nullable();
            $table->timestamps();
            $table->dateTime('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
