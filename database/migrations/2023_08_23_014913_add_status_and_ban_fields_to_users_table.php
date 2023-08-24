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
        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active');  // デフォルトとして'active'を設定
            $table->timestamp('banned_until')->nullable(); // 日時を格納。banされていない場合はNULL
            $table->text('ban_reason')->nullable();       // banの理由。banされていない場合はNULL
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('banned_until');
            $table->dropColumn('ban_reason');
        });
    }
};
