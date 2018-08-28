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

    protected $rftCustoms = [];

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
        return array_key_exists('service', $this->rftCustoms) ? $this->rftCustoms['service'] : null;
    }

    public function getSerialNo()
    {
        return array_key_exists('service', $this->rftCustoms) ? $this->rftCustoms['serial_no'] : null;
    }

    public function getThirdCustom()
    {
        return array_key_exists('service', $this->rftCustoms) ? $this->rftCustoms['third_custom'] : null;
    }

    /**
     * @param $thirdCustom
     * @return $this
     */
    public function setThirdCustom($thirdCustom)
    {
        $this->rftCustoms['third_custom'] = $thirdCustom;
        return $this;
    }

    /**
     * @param $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->rftCustoms['description'] = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return array_key_exists('service', $this->rftCustoms) ? $this->rftCustoms['description'] : null;
    }
}