<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 14:36
 */

namespace Sureyee\LaravelRockFinTech\Traits;


use Sureyee\LaravelRockFinTech\Facades\Rock;
use Sureyee\RockFinTech\Contracts\ResponseInterface;

class AccountTrait
{
    /**
     * @return ResponseInterface|bool
     */
    public function createAccount()
    {
        return Rock::createAccountP($this->rtfMobile);
    }

    public function getRtfMobileAttribute()
    {
        return $this->mobile;
    }
}