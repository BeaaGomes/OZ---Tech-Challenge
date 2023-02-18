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

            $table->unsignedBigInteger("external_id");
            $table->string("title");
            $table->string("url");
            $table->string("image_url");
            $table->string("news_site");
            $table->string("summary");
            $table->string("published_at");
            $table->string("updated_at");
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
