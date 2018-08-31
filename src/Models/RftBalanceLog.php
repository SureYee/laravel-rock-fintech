<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-31
 * Time: 14:00
 */

namespace Sureyee\LaravelRockFinTech\Models;


use Illuminate\Database\Eloquent\Model;

class RftBalanceLog extends Model
{
    protected $dates = [
        'transaction_date',
        'recorded_date',
    ];

    protected $guarded = ['id', 'created_at', 'updated_at'];
}