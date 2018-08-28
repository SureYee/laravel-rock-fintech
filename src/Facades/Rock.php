<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 15:15
 */

namespace Sureyee\LaravelRockFinTech\Facades;


use Illuminate\Support\Facades\Facade;
use Sureyee\RockFinTech\RockConfig;

/**
 * Class Rock
 * @package Sureyee\LaravelRockFinTech\Facades
 * @method static createAccountP($mobile)
 * @method static batchRepaymentB()
 * @method static boolean validSign(array $params)
 * @method static unfrozen(string $card_no, $amount, $origin_serial_no, $out_serial_no = null)
 * @method static revokeRepayment($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static revokePayment($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static signTransferP($card_no, $amount, $start_time, $end_time, $out_serial_no = null)
 * @method static revokeTransfer($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static assetsRevoke($asset_no, $card_no, $amount, $third_custom = null)
 * @method static revokeBid($card_no, $origin_serial_no, $amount, $asset_no, $out_serial_no = null)
 * @method static revokeAutoBid($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static revokeTrusteePay($card_no, $debt_card_no)
 * @method static revokeCreditTransfer($card_no, $origin_serial_no, $third_custom = null, $out_serial_no = null)
 * @method static revokeWarrant($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static moneyRevoke($origin_timestamp,$origin_serial_no,$card_no_in,$amount,$card_no = null,$currency = RockConfig::CNY,$description = null)
 * @method static batchRevokeBuyCreditB(array $items,$batch_no = null,$batch_date = null)
 */
class Rock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rock';
    }
}