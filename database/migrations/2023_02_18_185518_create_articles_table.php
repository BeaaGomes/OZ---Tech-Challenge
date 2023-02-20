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

            $table->unsignedBigInteger("external_id")->unique()->nullable();
            $table->string("title", 255);
            $table->string("url", 500);
            $table->string("image_url", 500)->nullable();
            $table->string("news_site", 255);
            $table->string("summary", 3000)->nullable();
            $table->dateTimeTz("published_at");
            $table->dateTimeTz("updated_at");
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
