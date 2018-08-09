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
 */
class Rock extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'rock';
    }
}