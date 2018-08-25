<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 15:15
 */

namespace Sureyee\LaravelRockFinTech\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * Class Rock
 * @package Sureyee\LaravelRockFinTech\Facades
 * @method static createAccountP()
 * @method static batchRepaymentB()
 * @method static boolean validSign(array $params)
 * @method static unfrozen(string $card_no, $amount, $origin_serial_no, $out_serial_no = null)
 * @method static revokeRepayment($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static revokePayment($card_no, $origin_serial_no, $out_serial_no = null)
 * @method static signTransferP($card_no, $amount, $start_time, $end_time, $out_serial_no = null)
 */
class Rock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rock';
    }
}