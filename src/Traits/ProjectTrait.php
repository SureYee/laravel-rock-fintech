<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-06
 * Time: 14:26
 */

namespace Sureyee\LaravelRockFinTech\Traits;


use Sureyee\LaravelRockFinTech\Rock;

class ProjectTrait
{

    /**
     * @return false|\Sureyee\RockFinTech\Response
     */
    public function batchRepayment()
    {
        return Rock::batchRepaymentB($this->rtfItems, $this->rtfBatchType);
    }
}