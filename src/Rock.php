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

/**
 * Class Rock
 * @package Sureyee\LaravelRockFinTech
 * @method
 */
class Rock
{

    const ACCOUNT_TYPE_BORROWER = 2;

    protected $client;

    protected $request;

    public function __construct(Client $client, Request $request)
    {
        $this->request = $request;
        $this->client = $client;
    }

    public function createAccountP($mobile, $account_type, $role_type, $out_serial_no = null)
    {
        $this->request->setService(snake_case(__FUNCTION__));
        $params = [
            'success_url' => Config::get('rock_fin_tech.success_url.create_account_p'),
            'mobile' => $mobile,
            'account_type' => $account_type,
            'role_type' => $role_type,
            'fail_url' => Config::get('rock_fin_tech.fail_url.create_account_p'),
            'out_serial_no' => is_null($out_serial_no) ? $this->uniqueId() : $out_serial_no,
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