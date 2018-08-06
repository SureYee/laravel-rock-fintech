<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-06
 * Time: 9:03
 */

namespace Sureyee\LaravelRockFinTech\Console;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class RockUp extends Command
{
    protected $signature = 'rock:up';

    protected $description = '启动钜石科技接口';

    public function handle()
    {
        if (Cache::has('rock_system_down') && Cache::forget('rock_system_down')) {
            $this->info('钜石科技接口维护结束！');
        }
        $this->error('钜石科技接口开启失败！');
    }
}