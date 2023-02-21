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
        Schema::create('articles', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("externalId")->unique()->nullable();
            $table->string("title", 255);
            $table->string("url", 500);
            $table->string("imageUrl", 500)->nullable();
            $table->string("newsSite", 255);
            $table->string("summary", 3000)->nullable();
            $table->dateTimeTz("publishedAt");
            $table->dateTimeTz("updatedAt");
            $table->boolean("featured");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
