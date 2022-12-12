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
        if (!Schema::hasTable('voicelist')) {
            Schema::create('voicelist', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id');
                $table->string('speech_id')->default(0);
                $table->string('speaker_id')->default(0);
                //削除フラグ
                $table->integer('delete_flg')->default(0);
                $table->timestamps();

                // 複合PK
                $table->unique(['speech_id']);
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
        Schema::dropIfExists('voicelist');
    }
};
