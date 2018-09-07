<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-09-07
 * Time: 21:44
 */

namespace Sureyee\LaravelRockFinTech\Responses;


use Sureyee\RockFinTech\Response;

class SyncResponse extends Response
{
    const TYPE = 'sync';

    public function getType()
    {
        return self::TYPE;
    }
}