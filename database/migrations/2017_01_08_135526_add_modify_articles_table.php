<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddModifyArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('article_versions', function ($table) {
            $table->renameColumn('content', 'html_content');
            $table->longText('text_content')->after('content')->comment('纯文本的文章');
            $table->text('description')->after('text_content')->comment('文章简介');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('article_versions', function ($table) {
            $table->renameColumn('html_content', 'content');
            $table->dropColumn('text_content');
            $table->dropColumn('description');
        });
    }
}
