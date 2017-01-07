<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticleVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_versions', function(Blueprint $table) {
            $table->increments('id');
//            $table->unsignedInteger('article_id');
            $table->string('cover_url')->nullable();
            $table->text('title')->nullable();
            $table->longText('content')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_versions');
    }
}
