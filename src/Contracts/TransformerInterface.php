<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-28
 * Time: 17:40
 */

namespace Sureyee\LaravelRockFinTech\Contracts;


interface TransformerInterface
{
    public function format($item):array;
}