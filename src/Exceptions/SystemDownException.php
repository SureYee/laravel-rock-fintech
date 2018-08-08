<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-06
 * Time: 10:05
 */

namespace Sureyee\LaravelRockFinTech\Exceptions;


use Throwable;

class SystemDownException extends \Exception
{
    public function __construct(string $message = "钜石接口系统维护中!", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}