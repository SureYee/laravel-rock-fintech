<?php

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