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
        Schema::create('file_containers', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id', 25)->unique();
            $table->string('file_extension', 20)->index();
            $table->string('unique_file_path')->index();
            $table->string('original_file_path');
            $table->unsignedInteger('total_accessed')->default(0);
            $table->foreignId('admin_id')->index()->nullable()->comment('uploaded by administrator');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('file_containers');
    }
};
