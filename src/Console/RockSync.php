<?php

namespace Sureyee\LaravelRockFinTech\Console;


use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use League\Flysystem\Filesystem;
use League\Flysystem\Sftp\SftpAdapter;
use Sureyee\LaravelRockFinTech\Exceptions\ArgumentValidFailedException;
use Sureyee\LaravelRockFinTech\Models\RftBalanceLog;

class RockSync extends Command
{
    protected $signature = 'rock:sync {date=today}';

    protected $description = '钜石科技读取每日交易记录';

    /**
     * @return int
     */
    public function handle()
    {
        try {
            $config = config('rock_fin_tech.sftp');
            $filesystem = new Filesystem(new SftpAdapter($config));

            $path = $this->getPath();

            if ($filesystem->has($path)) {

                $stream = $filesystem->readStream($path);

                while ($line = fgets($stream)) {
                    $this->saveData($this->parseContent($line));
                }
                fclose($stream);
                return 0;
            }
            throw new \Exception('文件 '. $path .' 不存在');
        } catch (\Exception $exception)  {
            $this->error($exception->getMessage());
            Log::error($exception->getMessage());
            return 1;
        }
    }

    /**
     * @return Carbon
     * @throws ArgumentValidFailedException
     */
    protected function getDateFromArgument()
    {
        $timestamp = strtotime($this->argument('date'));

        if (!$timestamp)
            throw new ArgumentValidFailedException('date 参数错误！');

        return Carbon::createFromTimestamp($timestamp);
    }

    protected function parseContent($content)
    {
        $data = explode('|', $content);
        $comment = explode('&', $data[13]);
        return [
            'log_no' => $data[0],
            'origin_id' => $data[1],
            'transaction_type' => $data[2],
            'transaction_date' => Carbon::createFromFormat('YmdHis', $data[3].$data[4]),
            'recorded_date' => Carbon::createFromFormat('Ymd', $data[5]),
            'serial_no' => $data[6],
            'flag' => $data[7],
            'card_no' => $data[8],
            'transaction_card_no' => $data[9],
            'transaction_money' => $data[10] === 'C' ? - $data[11] : $data[11],
            'transaction_account' => $data[12],
            'sequence_id' => array_pop($comment)
        ];

    }

    protected function saveData($data)
    {
        RftBalanceLog::create($data);
    }

    /**
     * @throws ArgumentValidFailedException
     */
    protected function getPath()
    {
        $date = $this->getDateFromArgument();
        return './download/' . $date->format('Ymd') . '/NEWALEVE' . config('rock_fin_tech.sftp_origin') . '-' . $date->copy()->subDay()->format('Ymd');
    }
}