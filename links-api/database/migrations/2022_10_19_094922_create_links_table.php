<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('causer_id')->nullable()->references('id')->on('users')->cascadeOnDelete();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('year')->nullable();
            $table->string('pages')->nullable();
            $table->string('language')->nullable();
            $table->string('image')->nullable();
            $table->string('country')->nullable();
            $table->string('link',512)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('links');
    }
}
