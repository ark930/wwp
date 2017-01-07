<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->char('tag', 10)->unique();
            $table->text('author');
            $table->unsignedInteger('publish_version_id')->nullable();
            $table->unsignedInteger('draft_version_id')->nullable();
            $table->enum('status', ['draft', 'published', 'published_with_draft', 'trashed']);
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
        Schema::dropIfExists('articles');
    }
}
