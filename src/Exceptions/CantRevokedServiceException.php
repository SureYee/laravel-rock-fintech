<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-24
 * Time: 19:22
 */

namespace Sureyee\LaravelRockFinTech\Exceptions;


class CantRevokedServiceException extends \Exception
{
    public function __construct(string $serivce)
    {

        $message = '当前服务[' . $serivce . ']不能被撤销！';
        parent::__construct($message);
    }
}