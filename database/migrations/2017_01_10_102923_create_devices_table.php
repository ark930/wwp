<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->nullable()->after('id');
            $table->char('tel', 11)->unique()->nullable()->change();

        });
        Schema::rename('users', 'devices');

        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->char('tel', 11)->unique();
            $table->string('username', 40)->unique()->nullable();
            $table->string('nickname', 40)->nullable();
            $table->string('avatar_url')->nullable();
            $table->timestamp('first_login_at')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->string('verify_code', 10)->nullable();
            $table->timestamp('verify_code_expire_at')->nullable();
            $table->timestamp('verify_code_refresh_at')->nullable();
            $table->unsignedInteger('verify_code_retry_times')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('articles', function ($table) {
            $table->renameColumn('user_id', 'device_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');

        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });

        Schema::rename('devices', 'users');
        Schema::table('articles', function ($table) {
            $table->renameColumn('device_id', 'user_id');
        });
    }
}
