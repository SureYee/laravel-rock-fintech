<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRftResponseLogsTable extends Migration
{
    public function up()
    {
        Schema::create('rft_response_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['async', 'sync'])->comment('回调类型');
            $table->string('code');
            $table->string('msg');
            $table->string('service')->comment('服务名称');
            $table->string('uuid');
            $table->string('version');
            $table->string('sequence_id');
            $table->string('custom');
            $table->dateTime('response_time');
            $table->text('response_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rft_response_logs');
    }
}