<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-08
 * Time: 10:18
 */

namespace Sureyee\LaravelRockFinTech\Console;


use Illuminate\Console\Command;
use Sureyee\LaravelRockFinTech\Facades\Rock;

class RockState extends Command
{
    protected $signature = 'rock:status';

    protected $description = '查看接口状态';

    public function handle()
    {
        $this->line('当前状态:');
        if (Rock::state()) {
            $this->info('启用中');
        } else {
            $lockTime = Rock::getSystemMaintenanceTime();
            $this->warn('维护中('.$lockTime[0].' - ' . $lockTime[1] ?? '永久' . ')');
        }
    }
}