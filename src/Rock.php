<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 15:13
 */

namespace Sureyee\LaravelRockFinTech;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Sureyee\LaravelRockFinTech\Exceptions\SystemDownException;
use Sureyee\RockFinTech\Client;
use Sureyee\RockFinTech\Exceptions\ResponseException;
use Sureyee\RockFinTech\Request;
use Sureyee\RockFinTech\RockConfig;

/**
 * Class Rock
 * @package Sureyee\LaravelRockFinTech
 * @method
 */
class Rock
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Request
     */
    protected $request;

    /**
     * 回调地址
     * @var string
     */
    protected $callback;

    public function __construct(Client $client, Request $request)
    {
        $this->request = $request;
        $this->client = $client;
        $this->callback = route('rft-callback');
    }

    //==================================  账户类接口 ==============================//

    /**
     * 个人|企业开户接口(页面)
     * @param $mobile
     * @param int $role_type
     * @param int $account_type
     * @param null $out_serial_no
     * @return false|\Sureyee\RockFinTech\Response
     * @throws
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
            'callback_url' => $this->callback,
            'out_serial_no' => is_null($out_serial_no) ? $this->uniqueId() : $out_serial_no,
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 重置密码（页面）
     * @param $customer_no
     * @param $card_no
     * @param null $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function setPasswordP($customer_no, $card_no, $out_serial_no = null)
    {
        $this->request->setService(snake_case(__FUNCTION__));

        $params = [
            'customer_no' => $customer_no,
            'card_no' => $card_no,
            'fail_url' => Config::get('rock_fin_tech.fail_url.create_account_p'),
            'success_url' => Config::get('rock_fin_tech.success_url.create_account_p'),
            'callback_url' => $this->callback,
            'out_serial_no' => is_null($out_serial_no) ? $this->uniqueId() : $out_serial_no,
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 解绑银行卡
     * @param $card_no
     * @param $bank_card_no
     * @param $customer_no
     * @param $serial_no
     * @param $bank_mobile
     * @param $card_type
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function unbindBankCard(
        $card_no,
        $bank_card_no,
        $customer_no,
        $serial_no,
        $bank_mobile,
        $card_type= RockConfig::CARD_TYPE_MAIN
    )
    {
        $this->request->setService(snake_case(__FUNCTION__));

        $params = [
            'card_no' =>$card_no,
            'bank_card_no' => $bank_card_no,
            'customer_no' => $customer_no,
            'serial_no' => $serial_no,
            'bank_mobile' => $bank_mobile,
            'card_type' => $card_type,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 电子账户查询
     * @param string $card_no 电子账号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function accountMobile($card_no)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(['card_no' => $card_no]);

        return $this->send();
    }

    /**
     * 电子账户余额查询
     * @param string $card_no 电子账号
     * @param string $customer_no 客户号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function accountBalance($card_no, $customer_no)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'customer_no' => $customer_no
            ]);

        return $this->send();
    }

    /**
     * @param string $card_no 电子账号
     * @param int $type 营销户类型
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function marketingQuery($card_no, $type = RockConfig::MARKETING_SERVICE_TYPE)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'type' => $type
            ]);

        return $this->send();
    }

    /**
     * 个人户按证件号查询电子账号
     * @param string $cert_no 证件号
     * @param int $cert_type 证件类型
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function findAccountById($cert_no, $cert_type = RockConfig::CERT_TYPE_ID_CARD)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'cert_no' => $cert_no,
                'cert_type' => $cert_type
            ]);

        return $this->send();
    }

    /**
     * 按手机号查询电子账号信息
     * @param string $mobile 手机号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function findAccountByMobile($mobile)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'mobile' => $mobile,
            ]);

        return $this->send();
    }

    /**
     * 投资人投资明细查询
     * @param string $card_no 	电子账号，必填，19（位数）
     * @param string|null $asset_no 标的编号，有条件必填，为空时查询所有的产品；不为空时按输入的产品发行方查询，40（位数）
     * @param int $state 查询的记录状态, 有条件必填，0：查询所有状态；1：投标中 2：放款中 3：计息中 4：本息已返回还，1（位数）
     * @param int|null $page_flag 翻页标志，有条件必填，首次查询上送空 ；翻页查询上送1，1（位数）
     * @param string|null $buy_date 投标日期，有条件必填，翻页控制使用；首次查询上送空；翻页查询时上送上页返回的最后一条记录的投标日期，8（位数）
     * @param string|null $out_serial_no 交易流水号，有条件必填 ，翻页控制使用；首次查询上送空；翻页查询时上送上页返回的最后一条记录的申请流水号，32（位数）
     * @param string|null $asset_page 标的编号，有条件必填 ，翻页控制使用；首次查询上送空；翻页查询时上送上页返回的最后一条记录的标的编号，6（位数）
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function accountCredits(
        $card_no,
        $asset_no = null,
        $state = RockConfig::SEARCH_STATE_ALL,
        $page_flag = RockConfig::PAGE_FLAG_TRUE,
        $buy_date = null,
        $out_serial_no = null,
        $asset_page = null)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'asset_no' => $asset_no,
                'state' => $state,
                'page_flag' => $page_flag,
                'buy_date' => $buy_date,
                'out_serial_no' => $out_serial_no,
                'asset_page' => $asset_page,
            ]);

        return $this->send();
    }

    /**
     * 账户业务流程查询
     * @param string $card_no 卡号，必填，电子账户，19（位数）
     * @param string $begin_date 起始记账日期，必填，格式：Ymd
     * @param string $end_date 	结束记账日期，必填，格式：Ymd
     * @param int $current_result 起始记录数，必填，大于等于1
     * @param int $total_result 查询记录条数，必填，不得超过99
     * @param null|int $type 流水类型，条件选填，默认所有交易，0：所有交易；1：转入；2：转出
     * @param null|int $order_by 排序，条件选填，默认正序 1：正序 2：倒序
     * @param null|int $record_flag 冲正标志位，条件选填，默认所有，Y是 N否
     * @param null|string $transact_type 交易类型，条件选填，默认所有流水，B：金融流水，N：非金融流水
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function accountTransactionQuery(
        $card_no,
        $begin_date,
        $end_date,
        $current_result = 1,
        $total_result = 20,
        $type = null,
        $order_by = null,
        $record_flag = null,
        $transact_type = null
        )
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(compact(
                'card_no', 'begin_date', 'end_date', 'current_result', 'total_result',
                'type', 'order_by', 'record_flag', 'transact_type'
            ));

        return $this->send();
    }


    /**
     * 开户结果查询接口
     * @param string $order_id 	订单号，32（位数）
     * @param int $account_type 账户类型(普通户200201,企业户200204), 选填, 不填默认200201
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function createAccountSrQuery($order_id, $account_type = RockConfig::ACCOUNT_TYPE_COMMON)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(compact(
                'order_id', 'account_type'
            ));

        return $this->send();
    }

    /**
     * 网关自定义下单查询
     * @param string $order_id 订单号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function gatewayQuery($order_id)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(compact('order_id'));

        return $this->send();
    }

    /**
     * 网关重置密码查询接口
     * @param string $order_id 订单号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function setPasswordQuery($order_id)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(compact('order_id'));

        return $this->send();
    }

    /**
     * 绑卡网关接口（页面）
     * @param string $card_no 电子账户
     * @param int $card_type 绑定卡类型
     * @param null|string $out_serial_no 流水号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function bindBankCardP($card_no,$card_type = RockConfig::CARD_TYPE_MAIN, $out_serial_no = null )
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'card_type' => $card_type,
                'out_serial_no' => $out_serial_no ?? uniqueId32(),
                'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
                'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
                'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
                'callback_url' => $this->callback,
            ]);

        return $this->send();
    }

    // ==============================  资金类接口 ==================================//

    /**
     * 资金冻结
     * @param string $card_no 电子账号
     * @param float $amount 冻结金额
     * @param null|string $out_serial_no 申请流水号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function frozen($card_no, $amount, $out_serial_no = null)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'amount' =>$amount,
                'out_serial_no' => $out_serial_no ?? uniqueId32(),
            ]);

        return $this->send();
    }

    /**
     * @param string $card_no 电子账号
     * @param float $amount 原冻结金额
     * @param string $origin_serial_no 原交易流水号
     * @param null|string $out_serial_no 交易流水号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function unfrozen($card_no, $amount, $origin_serial_no, $out_serial_no = null)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'amount' => $amount,
                'out_serial_no' => $out_serial_no ?? uniqueId32(),
                'origin_serial_no' => $origin_serial_no,
            ]);

        return $this->send();
    }

    /**
     * 资金冻结查询
     * @param string $card_no 交易代码
     * @param string $origin_serial_no 原资金冻结交易流水号
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function frozenQuery($card_no, $origin_serial_no)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams(compact('card_no', 'origin_serial_no'));

        return $this->send();
    }

    /**
     * 营销账户充值
     * @param float $amount 金额
     * @param null|string $serial_no 交易流水号
     * @param int $currency 币种
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function couponRecharge($amount, $serial_no = null, $currency = RockConfig::CNY)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'amount' => $amount,
                'serial_no' => $serial_no ?? uniqueId32(),
                'currency' => $currency
            ]);

        return $this->send();
    }

    /**
     * @param float $amount 金额
     * @param null|string $serial_no 交易流水号
     * @param int $currency 币种
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function couponWithdraw($amount, $serial_no = null, $currency = RockConfig::CNY)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'amount' => $amount,
                'serial_no' => $serial_no ?? uniqueId32(),
                'currency' => $currency
            ]);

        return $this->send();
    }

    /**
     * @param string $card_no 电子账户，必填,19(位数)
     * @param string $bank_name 银行名称，必填,60(位数)
     * @param string $bank_id_no 银行代码，必填
     * @param float $amount 充值金额,必填,精确到分，13(位数)
     * @param float $fee 手续费,选填,最大12位,保留两位
     * @param null $order_no
     * @param $bank_type
     * @param null $customer_no
     * @param null $product_name
     * @param null $product_detail
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function bankRecharge(
        $card_no,
        $bank_name,
        $bank_id_no,
        $amount,
        $fee = 0.00,
        $order_no = null,
        $bank_type = RockConfig::BANK_TYPE_COMMON,
        $customer_no = null,
        $product_name = null,
        $product_detail = null)
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'card_no' => $card_no,
                'bank_type' => $bank_type,
                'callback_url' => $this->callback,
                'customer_no' => $customer_no,
                'redirect_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
                'product_name' => $product_name,
                'product_detail' => $product_detail,
                'order_no' => $order_no ?? uniqueId32(),
                'bank_name' => $bank_name,
                'bank_id_no' => $bank_id_no,
                'amount' => $amount,
                'fee' => $fee,
        ]);

        return $this->send();
    }

    public function rechargeP(
        $card_no,
        $bind_card,
        $cert_no,
        $name,
        $mobile,
        $amount,
        $auth_flag,
        $fee = 0.00,
        $order_no = null,
        $cert_type = RockConfig::CERT_TYPE_ID_CARD,
        $currency = RockConfig::CNY,
        $auth_seq_id = null,
        $user_bank_code = null,
        $user_bank_name_en = null,
        $user_bank_name_cn = null,
        $bank_province = null,
        $bank_city = null,
        $user_ip = null
    )
    {
        $this->request->setService(snake_case(__FUNCTION__))
            ->setParams([
                'order_no' => $order_no ?? uniqueId32(),
                'card_no' => $card_no,
                'bind_card' => $bind_card,
                'currency' => $currency,
                'amount' => $amount,
                'fee' => $fee,
                'cert_type' => $cert_type,
                'cert_no'   => $cert_no,
                'name' => $name,
                'mobile' => $mobile,
                'callback' => $this->callback,
                'auth_flag' => $auth_flag,
                'auth_seq_id' => $auth_seq_id,
                'user_bank_code' => $user_bank_code,
                'user_bank_name_en' => $user_bank_name_en,
                'user_bank_name_cn' => $user_bank_name_cn,
                'bank_province' => $bank_province,
                'bank_city' => $bank_city,
                'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
                'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
                'user_ip' => $user_ip,
                'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
            ]);

        return $this->send();
    }

    // =============================  批量处理接口 =================================//

    /**
     * 批次还款
     * @param array $items
     * @param string $batch_type
     * @param null $batch_no
     * @param null $batch_date
     * @return false|\Sureyee\RockFinTech\Response
     * @throws
     */
    public function batchRepaymentB(array $items, $batch_type = RockConfig::BATCH_TYPE_REPAY, $batch_no = null,  $batch_date = null)
    {
        $this->request->setService(snake_case(__FUNCTION__));

        $params = [
            'batch_no' => $batch_no ?? uniqid(),
            'batch_type' => $batch_type,
            'batch_count' => count($items),
            'batch_date' => $batch_date ?? date('Ymd'),
            'notify_url' => $this->callback,
            'items' => $items
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 发送请求
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    protected function send()
    {
        // 系统状态验证
        if (Cache::has('rock_system_down') && Cache::get('rock_system_down')[0] <= now()) {
            throw new SystemDownException('系统维护中!');
        }

        try {
            return $this->client->request($this->request);
        } catch (ResponseException $exception) {
            Log::error($exception->getMessage(), $this->request->getParams());
        }
        return false;
    }

    /**
     * 验签
     * @param array $params
     * @return bool
     */
    public function validSign(array $params):bool
    {
        return $this->client->validSign($params);
    }

    /**
     * 生成32位唯一编号
     * @return string
     */
    protected function uniqueId()
    {
        return md5(uniqid(md5(microtime(true)),true));
    }

    /**
     * 获取系统维护时间
     * @return null|array
     */
    public function getSystemMaintenanceTime()
    {
        if (Cache::has('rock_system_down')) {
            return Cache::get('rock_system_down');
        } else {
            return null;
        }
    }
}