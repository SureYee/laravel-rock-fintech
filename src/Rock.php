<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 15:13
 */

namespace Sureyee\LaravelRockFinTech;


use Sureyee\RockFinTech\Request;

class Rock
{

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
        dd(__METHOD__);
    }

    public function createAccount()
    {

    }
}