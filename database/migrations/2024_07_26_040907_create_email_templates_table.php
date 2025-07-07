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
        Schema::create(env('PREFIX_TABLE') . 'email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('unique_name')->unique()->comment('example: user-signup, user-forgot-password, user-reset-password');
            $table->string('subject');
            $table->text('cc')->nullable()->comment('Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com');
            $table->text('bcc')->nullable()->comment('Use a comma (,) without spaces to separate email addresses, e.g. mail1@domain.com,mail2@domain.com');
            $table->string('reply_to')->nullable();
            $table->longText('email_body');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists(env('PREFIX_TABLE') . 'email_templates');
    }
};
