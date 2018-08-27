<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-25
 * Time: 9:50
 */

namespace Sureyee\LaravelRockFinTech\Traits;


use Sureyee\RockFinTech\Request;

trait RequestTrait
{

    protected $rftThirdCustom = null;

    /**
     * @return Request
     */
    public function getRequestData()
    {
        return unserialize($this->request_data);
    }

    /**
     * @return string
     */
    public function getService()
    {
        return $this->service;
    }

    public function getSerialNo()
    {
        return $this->serial_no;
    }

    public function getThirdCustom()
    {
        return $this->rftThirdCustom;
    }

    /**
     * @param $thirdCustom
     * @return $this
     */
    public function setThirdCustom($thirdCustom)
    {
        $this->rftThirdCustom = $thirdCustom;
        return $this;
    }
}