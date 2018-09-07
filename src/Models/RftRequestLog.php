<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RftRequestLog extends Model
{
    protected $guarded = ['id'];

    public function responses()
    {
        return $this->hasMany(RftResponseLog::class, 'uuid', 'uuid');
    }

    /**
     * @param $value
     * @return array
     */
    public function getRequestDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
