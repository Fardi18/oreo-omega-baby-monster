<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfficesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(env('PREFIX_TABLE') . 'offices', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('logo', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('phone', 100)->nullable();
            $table->string('fax', 100)->nullable();
            $table->string('email_office', 100)->nullable();
            $table->string('email_contact', 100)->nullable();
            $table->string('wa_phone', 100)->nullable();
            $table->text('address')->nullable();
            $table->text('gmaps')->nullable();
            $table->boolean('status')->default(1);
            $table->integer('ordinal');
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
        Schema::dropIfExists(env('PREFIX_TABLE') . 'offices');
    }
}