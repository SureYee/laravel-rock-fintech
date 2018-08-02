<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 15:13
 */

namespace Sureyee\LaravelRockFinTech;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Sureyee\RockFinTech\Client;
use Sureyee\RockFinTech\Request;
use Sureyee\RockFinTech\RockConfig;

/**
 * Class Rock
 * @package Sureyee\LaravelRockFinTech
 * @method
 */
class Rock
{

    protected $client;

    protected $request;

    public function __construct(Client $client, Request $request)
    {
        $this->request = $request;
        $this->client = $client;
    }

    /**
     * 个人|企业开户接口(页面)
     * @param $mobile
     * @param int $role_type
     * @param int $account_type
     * @param null $out_serial_no
     * @return false|\Sureyee\RockFinTech\Response
     */
    public function createAccountP($mobile, $role_type = RockConfig::ROLE_TYPE_BORROWER, $account_type = RockConfig::ACCOUNT_TYPE_COMMON,  $out_serial_no = null)
    {
        $this->request->setService(snake_case(__FUNCTION__));
        $params = [
            'mobile' => $mobile,
            'account_type' => $account_type,
            'role_type' => $role_type,
            'fail_url' => Config::get('rock_fin_tech.fail_url.create_account_p'),
            'success_url' => Config::get('rock_fin_tech.success_url.create_account_p'),
            'callback_url' => route('rft-callback'),
            'out_serial_no' => is_null($out_serial_no) ? $this->uniqueId() : $out_serial_no,
        ];

        $this->request->setParams($params);

        return $this->send();

    }

    /**
     * 批次还款
     * @param array $items
     * @param string $batch_type
     * @param null $batch_no
     * @param null $batch_date
     * @return false|\Sureyee\RockFinTech\Response
     */
    public function batchRepaymentB(array $items, $batch_type = RockConfig::BATCH_TYPE_REPAY, $batch_no = null,  $batch_date = null)
    {
        $this->request->setService(snake_case(__FUNCTION__));

        $params = [
            'batch_no' => $batch_no ?? uniqid(),
            'batch_type' => $batch_type,
            'batch_count' => count($items),
            'batch_date' => $batch_date ?? date('Ymd'),
            'items' => $items
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 发送请求
     * @return \Sureyee\RockFinTech\Response|false
     */
    protected function send()
    {
        try {
            return $this->client->request($this->request);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage(), $this->request->getParams());
        }
        return false;
    }

    /**
     * 生成32位唯一编号
     * @return string
     */
    protected function uniqueId()
    {
        return md5(uniqid(md5(microtime(true)),true));
    }
}