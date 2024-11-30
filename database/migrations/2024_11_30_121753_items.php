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
        Schema::create('items', function (Blueprint $table) {
            $table->id("id");
            $table->string("name_ar", 255);
            $table->string("name", 255);
            $table->string("description_ar")->nullable();
            $table->string("description")->nullable();
            $table->string("image", 100)->nullable();
            $table->integer("quantity");
            $table->tinyInteger("is_active");
            $table->float("price");
            $table->smallInteger("discount")->nullable();
            $table->bigInteger('category_id')->unsigned();
            $table->foreign("category_id")->references('id')->on('categories')->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
