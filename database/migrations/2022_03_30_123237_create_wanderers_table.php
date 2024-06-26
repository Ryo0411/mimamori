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
        if (!Schema::hasTable('wanderers')) {
            Schema::create('wanderers', function (Blueprint $table) {
                $table->id();
                $table->string('wanderer_name')->nullable();
                $table->string('family_name')->nullable();
                $table->string('email')->nullable();
                $table->tinyInteger('sex')->default(0);
                $table->integer('age')->nullable();
                $table->integer('user_id');
                $table->string('profile_id')->default(0);
                //緊急連絡先
                $table->string('emergency_tel')->nullable();
                //迷子者フラグ
                $table->tinyInteger('wandering_flg')->default(0);
                //発見フラグ
                $table->tinyInteger('discover_flg')->default(0);
                //声紋登録フラグ
                $table->integer('voiceprint_flg')->default(0);
                //発見者ID
                $table->integer('wanderer_id')->default(0);
                //発見日時
                $table->datetime('wanderer_time')->default(date("Y-m-d H:i:s"));
                // 発見時の緯度経度
                $table->double('latitude', 10, 7)->nullable();
                $table->double('longitude', 10, 7)->nullable();
                $table->timestamps();

                // 複合PK
                $table->unique(['user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wanderers');
    }
};
