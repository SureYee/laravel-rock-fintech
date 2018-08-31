<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRftBalanceLogsTable extends Migration
{
    public function up()
    {
        Schema::create('rft_balance_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('log_no');
            $table->string('origin_id');
            $table->string('transaction_type');
            $table->dateTime('transaction_date');
            $table->date('recorded_date');
            $table->string('serial_no');
            $table->string('flag', 1)->comment('冲正标志位');
            $table->string('card_no')->comment('交易卡号');
            $table->string('transaction_card_no')->nullable()->comment('对手交易账号');
            $table->string('transaction_symbol')->comment('交易金额符号');
            $table->float('transaction_money')->comment('交易金额');
            $table->float('transaction_account', 12, 2)->comment('交易后余额');
            $table->string('sequence_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rft_balance_logs');
    }
}