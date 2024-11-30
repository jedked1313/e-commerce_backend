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
            $table->id("user_id");
            $table->string("user_name",100);
            $table->string("user_email",100)->unique();
            $table->string("user_password",255);
            $table->integer("user_verifycode");
            $table->tinyInteger("user_approve")->default("0");
            $table->string("user_image")->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamps();
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
