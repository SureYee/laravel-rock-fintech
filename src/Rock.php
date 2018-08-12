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
use Sureyee\LaravelRockFinTech\Events\RockBeforeRequest;
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
     * @var string $custom
     */
    protected $custom;

    /**
     * 回调地址
     * @var string
     */
    protected $callback;

    public function __construct(Client $client)
    {
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
        $params = [
            'mobile' => $mobile,
            'account_type' => $account_type,
            'role_type' => $role_type,
            'fail_url' => Config::get('rock_fin_tech.fail_url.create_account_p'),
            'success_url' => Config::get('rock_fin_tech.success_url.create_account_p'),
            'callback_url' => $this->callback,
            'out_serial_no' => is_null($out_serial_no) ? uniqueId32() : $out_serial_no,
        ];
        $this->request = new Request(snake_case(__FUNCTION__));
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $params = [
            'customer_no' => $customer_no,
            'card_no' => $card_no,
            'fail_url' => Config::get('rock_fin_tech.fail_url.create_account_p'),
            'success_url' => Config::get('rock_fin_tech.success_url.create_account_p'),
            'callback_url' => $this->callback,
            'out_serial_no' => is_null($out_serial_no) ? uniqueId32() : $out_serial_no,
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
        $this->request = new Request(snake_case(__FUNCTION__));

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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(['card_no' => $card_no]);

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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact(
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact(
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('order_id'));

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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('order_id'));

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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('card_no', 'origin_serial_no'));

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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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

    /**
     * 绑定卡到电子账户充值（页面）
     * @param string $card_no 电子账户，必填,19(位数)
     * @param string $bind_card 绑定卡卡号 ，必填，esb校验，19(位数)
     * @param string $cert_no 证件号码 ，必填，18(位数)
     * @param string $name 姓名 ，必填，60(位数)
     * @param string $mobile 手机号码 ，必填，11(位数)
     * @param string $amount 金额 ，必填，精确到分，13(位数)
     * @param string $auth_flag ESB代发实名认证标志 ，必填，首次充值上送Y，之后充值上送N，1(位数)
     * @param float $fee 充值手续费 ，必填，精确到分，12位保留两位
     * @param null $order_no 订单编号 ,必填,32(位数)
     * @param int $cert_type 证件类型 ， 15 ，必填，2(位数)
     * @param int $currency 币种 ，必填，156，3(位数)
     * @param null|string $auth_seq_id 实名认证流水号 ，条件可选，6(位数)
     * @param null|string $user_bank_code 开户银行代码，条件可选 ，20(位数)
     * @param null|string $user_bank_name_en 开户银行英文缩写，条件可选 ，20(位数)
     * @param null|string $user_bank_name_cn 开户银行中文名称，条件可选 ，50(位数)
     * @param null|string $bank_province 开户行省份，条件可选，20(位数)
     * @param null|string $bank_city 开户行城市，条件可选，50(位数)
     * @param null|string $user_ip 客户IP，条件可选，32(位数)
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
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
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
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
                'callback_url' => $this->callback,
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

    /**
     * 提现（页面）
     * @param string $card_no 电子账户 ,必填,19(位数)
     * @param string $bind_card 绑定卡号,必填,19(位数)
     * @param string $bank_name 银行名称,大额提现必填,60(位数
     * @param string $cert_no 证件号码 , 必填,18(位数)
     * @param string $name 姓名 , 必填,60(位数)
     * @param string $mobile 手机号码 ，必填,11(位数)
     * @param float $amount 提现金额 ，必填,13位保留两位
     * @param float $fee 手续费，必填,13位保留两位
     * @param null|string $order_no 订单编号 ,必填,32(位数)
     * @param int $sms_switch 必填,1位 备注:是否使用短信验证码(1:使用,其他:不使用)
     * @param int $cert_type 证件类型 , 必填，15-身份证18位，20-其它，25-企业社会信用代码 注：企业开户时上送20或社会信用号25 ,2(位数)
     * @param null $channel_flag
     * @param null $channel_code
     * @param null $union_bank_code
     * @param null $open_bank_code
     * @param null $bank_name_en
     * @param null $bank_name_cn
     * @param null $bank_province
     * @param null $bank_city
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function withdrawP (
        $card_no,
        $bind_card,
        $bank_name,
        $cert_no,
        $name,
        $mobile,
        $amount,
        $fee = 0.00,
        $order_no = null,
        $sms_switch = 1,
        $cert_type = RockConfig::CERT_TYPE_ID_CARD,
        $channel_flag = null,
        $channel_code = null,
        $union_bank_code = null,
        $open_bank_code = null,
        $bank_name_en = null,
        $bank_name_cn = null,
        $bank_province = null,
        $bank_city = null
    )
    {
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams([
                'order_no' => $order_no ?? uniqueId32(),
                'card_no' => $card_no,
                'bind_card' => $bind_card,
                'amount' => $amount,
                'fee' => $fee,
                'cert_type' => $cert_type,
                'cert_no'   => $cert_no,
                'name' => $name,
                'mobile' => $mobile,
                'callback_url' => $this->callback,
                'bank_province' => $bank_province,
                'bank_city' => $bank_city,
                'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
                'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
                'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
                'bank_name' => $bank_name,
                'sms_switch' => $sms_switch,
                'channel_flag' => $channel_flag,
                'channel_code' => $channel_code,
                'union_bank_code' => $union_bank_code,
                'open_bank_code' => $open_bank_code,
                'bank_name_en' => $bank_name_en,
                'bank_name_cn' => $bank_name_cn
            ]);

        return $this->send();
    }

    /**
     * 借款人放款手续费和还款金额签约（页面）
     * @param $card_no
     * @param $payment_amount
     * @param $repayment_amount
     * @param null|string $out_serial_no
     * @param $payment_start_time
     * @param $repayment_start_time
     * @param $payment_end_time
     * @param $repayment_end_time
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signBorrowerP (
        $card_no,
        $payment_amount,
        $repayment_amount,
        $payment_start_time,
        $repayment_start_time,
        $payment_end_time,
        $repayment_end_time,
        $out_serial_no = null
    )
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'payment_amount' => $payment_amount,
            'repayment_amount' => $repayment_amount,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'payment_start_time' => $payment_start_time,
            'repayment_start_time' => $repayment_start_time,
            'payment_end_time' => $payment_end_time,
            'repayment_end_time' => $repayment_end_time,
            'callback_url' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 放款手续费签约查询
     * @param $card_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signPaymentQuery($card_no)
    {
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('card_no'));

        return $this->send();
    }

    /**
     * 借款人还款金额签约查询
     * @param $card_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signRepaymentQuery($card_no)
    {
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('card_no'));

        return $this->send();
    }

    /**
     * 撤销借款人还款金额签约
     * @param string $card_no
     * @param string $origin_serial_no
     * @param null|string $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function revokeRepayment($card_no, $origin_serial_no, $out_serial_no = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'origin_serial_no' => $origin_serial_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 撤销放款手续费签约
     * @param $card_no
     * @param $origin_serial_no
     * @param null $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function revokePayment($card_no, $origin_serial_no, $out_serial_no = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'origin_serial_no' => $origin_serial_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 转让方转让手续费签约（页面）
     * @param $card_no
     * @param $amount
     * @param $start_time
     * @param $end_time
     * @param null $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signTransferP($card_no, $amount, $start_time, $end_time, $out_serial_no = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'amount' => $amount,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'callback' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);

        return $this->send();
    }

    /**
     * 转让手续费签约查询
     * @param $card_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signTransferCheck($card_no)
    {
        $this->request = new Request(snake_case(__FUNCTION__));
        $this->request->setParams(compact('card_no'));

        return $this->send();
    }

    /**
     * 撤销转让方转让手续费签约
     * @param $card_no
     * @param $origin_serial_no
     * @param $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function revokeTransfer($card_no, $origin_serial_no, $out_serial_no)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'origin_serial_no' => $origin_serial_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 签约累加（页面）
     * @param $card_no
     * @param $origin_serial_no
     * @param $interface_type
     * @param $unit_amount
     * @param $amount
     * @param $start_time
     * @param $end_time
     * @param null $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signAgainP(
        $card_no,
        $origin_serial_no,
        $interface_type,
        $unit_amount,
        $amount,
        $start_time,
        $end_time,
        $out_serial_no = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'origin_serial_no' => $origin_serial_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'interface_type' => $interface_type,
            'unit_amount' => $unit_amount,
            'amount' => $amount,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'callback_url' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 放款手续费签约（页面）
     * @param $card_no
     * @param $amount
     * @param $start_time
     * @param $end_time
     * @param null $out_serial_no
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function signFeeP($card_no, $amount, $start_time, $end_time, $out_serial_no = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'amount' => $amount,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'callback_url' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    // ============================= 资产类接口 =================================//

    /**
     * 借款人标的登记
     * @param $asset_no
     * @param $asset_brief
     * @param $card_no
     * @param $amount
     * @param $interest_type
     * @param $interest_day
     * @param $loan_term
     * @param $interest_rate
     * @param null $warrant_card_no
     * @param null $borrow_card_no
     * @param null $debtor_card_no
     * @param $trustee_pay_flag
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function assetsEnroll(
        $asset_no,
        $asset_brief,
        $card_no,
        $amount,
        $interest_type,
        $interest_day,
        $loan_term,
        $interest_rate,
        $warrant_card_no = null,
        $borrow_card_no = null,
        $debtor_card_no = null,
        $trustee_pay_flag = RockConfig::TRUSTEE_PAY_FLAG_NORMAL,
        $third_custom = null
    )
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'asset_no' => $asset_no,
            'asset_brief' => $asset_brief,
            'card_no' => $card_no,
            'amount' => $amount,
            'interest_type' => $interest_type,
            'interest_day' => $interest_day,
            'loan_term' => $loan_term,
            'interest_rate' => $interest_rate,
            'warrant_card_no' => $warrant_card_no,
            'borrow_card_no	' => $borrow_card_no,
            'debtor_card_no' => $debtor_card_no,
            'trustee_pay_flag' => $trustee_pay_flag,
            'third_custom' => $third_custom,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 借款人标的撤销
     * @param $asset_no
     * @param $card_no
     * @param $amount
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function assetsRevoke($asset_no, $card_no, $amount, $third_custom = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'asset_no' => $asset_no,
            'card_no' => $card_no,
            'amount' => $amount,
            'third_custom' => $third_custom,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 借款人标的查询
     * @param $asset_no
     * @param $card_no
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function assetQuery($asset_no, $card_no, $third_custom = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'asset_no' => $asset_no,
            'card_no' => $card_no,
            'third_custom' => $third_custom,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 修改标的信息
     * @param $asset_no
     * @param $card_no
     * @param $warranty_card_no
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function assetChange($asset_no, $card_no, $warranty_card_no, $third_custom = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'asset_no' => $asset_no,
            'card_no' => $card_no,
            'warranty_card_no' => $warranty_card_no,
            'third_custom' => $third_custom,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 再贷标的余额查询
     * @param $card_no
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function assetBalanceQuery($card_no, $third_custom = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'third_custom' => $third_custom,
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    // ============================= 交易类接口 =================================//

    /**
     * 投资人投标申请（页面）
     * @param $card_no
     * @param $amount
     * @param $asset_no
     * @param $interest_date
     * @param $interest_type
     * @param $end_date
     * @param $interest_rate
     * @param bool $use_bonus
     * @param string $bonus_amount
     * @param null $transact_date
     * @param bool $frozen_flag
     * @param null $interest_day
     * @param null $out_serial_no
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function bidApplyP(
        $card_no,
        $amount,
        $asset_no,
        $interest_date,
        $interest_type,
        $end_date,
        $interest_rate,
        $use_bonus = false,
        $bonus_amount = "0.00",
        $transact_date = null,
        $frozen_flag = true,
        $interest_day = null,
        $out_serial_no = null,
        $third_custom = null)
    {
        $this->request = new Request(snake_case(__FUNCTION__));

        $params = [
            'card_no' => $card_no,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'amount' => $amount,
            'asset_no' => $asset_no,
            'interest_date' => $interest_date,
            'interest_type' => $interest_type,
            'interest_day' => $interest_day,
            'end_date' => $end_date,
            'interest_rate' => $interest_rate,
            'frozen_flag' => $frozen_flag,
            'use_bonus' => $use_bonus,
            'bonus_amount' => $bonus_amount,
            'transact_date' => $transact_date ?? date('Y-m-d'),
            'third_custom' => $third_custom,
            'callback_url' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    /**
     * 投资人购买债权（页面）
     * @param $card_no_in
     * @param $origin_serial_no
     * @param $card_no_out
     * @param $total_balance
     * @param $amount
     * @param $transfer_price
     * @param $interest_date
     * @param $interest_rate
     * @param $fee_way
     * @param $fee
     * @param $mobile
     * @param null $out_serial_no
     * @param null $third_custom
     * @return bool|\Sureyee\RockFinTech\Response
     * @throws SystemDownException
     * @throws \Sureyee\RockFinTech\Exceptions\DecryptException
     * @throws \Sureyee\RockFinTech\Exceptions\RsaKeyNotFoundException
     */
    public function buyCreditP(
        $card_no_in,
        $origin_serial_no,
        $card_no_out,
        $total_balance,
        $amount,
        $transfer_price,
        $interest_date,
        $interest_rate,
        $fee_way,
        $fee,
        $mobile,
        $out_serial_no = null,
        $third_custom = null
    )
    {
        $this->request = new Request(snake_case(__FUNCTION__));
        $params = [
            'card_no_in' => $card_no_in,
            'out_serial_no' => $out_serial_no ?? uniqueId32(),
            'origin_serial_no' => $origin_serial_no,
            'card_no_out' => $card_no_out,
            'total_balance' => $total_balance,
            'amount' => $amount,
            'transfer_price' => $transfer_price,
            'interest_date' => $interest_date,
            'interest_rate' => $interest_rate,
            'fee_way' => $fee_way,
            'fee' => $fee,
            'third_custom' => $third_custom,
            'mobile' => $mobile,
            'callback_url' => $this->callback,
            'success_url' => Config::get('rock_fin_tech.success_url.' . snake_case(__FUNCTION__)),
            'fail_url' => Config::get('rock_fin_tech.fail_url.' . snake_case(__FUNCTION__)),
            'forget_pwd_url' => Config::get('rock_fin_tech.forget_pwd_url.' . snake_case(__FUNCTION__)),
        ];

        $this->request->setParams($params);
        return $this->send();
    }

    // =============================  查询类接口 =================================//

    public function batchQueryRepaymentB(
        $batch_no,
        $batch_count,
        $batch_type = RockConfig::BATCH_TYPE_REPAY
    )
    {

    }

    // =============================  批量处理接口 =================================//

    /**
     * 批次还款
     * @param $items
     * @param string $batch_type 业务类别 ,必填， 001-放款 002-到期还款 003-平台逾期代偿/担保公司代偿,(3)位数
     * @param null $batch_no
     * @param null $batch_date
     * @return false|\Sureyee\RockFinTech\Response
     * @throws
     */
    public function batchRepaymentB(
        array $items,
        $batch_type = RockConfig::BATCH_TYPE_REPAY,
        $batch_no = null,
        $batch_date = null
    )
    {
        $this->request = new Request(snake_case(__FUNCTION__));

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
     * 设置custom参数
     * @param string $custom
     * @return Rock
     */
    public function custom(string $custom):Rock
    {
        $this->custom = $custom;
        return $this;
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
        if ($this->isRunning()) {
            try {
                $this->request->custom = $this->custom;
                Log::debug('rock-fin-tech request:' .  $this->request->toJson());
                event(new RockBeforeRequest($this->request));
                $response =  $this->client->request($this->request);
                event(new )
            } catch (ResponseException $exception) {
                Log::error($exception->getMessage(), $this->request->getParams());
            }
            return false;
        }
        throw new SystemDownException();

    }

    /**
     * 验证系统是否处于运行状态
     * @return bool
     */
    public function isRunning()
    {
        return !(Cache::has('rock_system_down') && Cache::get('rock_system_down')[0] <= now()->timestamp);
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