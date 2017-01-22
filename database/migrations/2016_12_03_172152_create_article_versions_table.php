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
            $table->string('cover_url')->nullable();
            $table->text('title')->nullable();
            $table->longText('html_content')->nullable();
            $table->longText('text_content')->comment('纯文本的文章');
            $table->text('description')->comment('文章简介');
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
