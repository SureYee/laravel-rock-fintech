<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-09-07
 * Time: 21:45
 */

namespace Sureyee\LaravelRockFinTech\Responses;


use Sureyee\RockFinTech\Response;

class AsyncResponse extends Response
{
    const TYPE = 'async';

    public function getType()
    {
        return self::TYPE;
    }
}