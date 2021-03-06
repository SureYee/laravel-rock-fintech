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

class RockDown extends Command
{
    protected $signature = 'rock:down {start=now} {--h=0}';

    protected $description = '钜石科技接口维护';

    public function handle()
    {
        $start = $this->argument('start');
        $hours = (float) $this->option('h');

        $start = strtotime($start);

        if ($start === false) {
            $this->error('请输入正确的系统开始暂停时间，如：12:00');
            return 1;
        }

        if (!is_numeric($hours)) {
            $this->error('请输入正确的系统暂停时长数值！');
            return 1;
        }

        $end = $hours === 0.0 ? null : $hours * 3600 + $start;

        $this->systemDown($start, $end);

        $this->info('钜石科技接口系统进入维护！维护时间(' . date('Y-m-d H:i:s', $start) .($end ? ' - ' . date('Y-m-d H:i:s', $end) : '') . ')');
        return 0;
    }

    protected function systemDown($start = null, $end = null)
    {
        $start = $start ?? now();
        if ($end === null) {
            Cache::forever('rock_system_down', [$start, $end]);
        } else {
            Cache::put('rock_system_down', [$start, $end], ($end-$start) / 60);
        }
    }
}