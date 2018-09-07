<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RftResponseLog extends Model
{
    protected $guarded = ['id'];

    public function request()
    {
        return $this->belongsTo(RftRequestLog::class, 'uuid', 'uuid');
    }

    public function getResponseDataAttribute($value)
    {
        return json_decode($value, true);
    }
}
