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
        Schema::create('address', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained('users', 'user_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('city', 255);
            $table->string('neighborhood', 255);
            $table->string('street', 255)->nullable();
            $table->string('contact_phone', 20);
            $table->string('address_name', 100)->nullable();
            $table->string('building', 100)->nullable();
            $table->string('apartment', 100)->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('postal_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('address');
    }
};
