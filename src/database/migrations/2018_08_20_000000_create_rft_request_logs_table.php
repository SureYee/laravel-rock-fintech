<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRftRequestLogsTable extends Migration
{
    public function up()
    {
        Schema::create('rft_request_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch_no')->nullable()->comment('批次请求的批次号');
            $table->string('serial_no')->nullable()->comment('交易流水号或订单号');
            $table->string('service')->comment('服务名称');
            $table->string('uuid')->comemnt('请求UUID')->unique();
            $table->string('client');
            $table->string('version');
            $table->string('custom');
            $table->dateTime('request_time');
            $table->text('request_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rft_request_logs');
    }
}