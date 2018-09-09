<?php

namespace Sureyee\LaravelRockFinTech\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Sureyee\RockFinTech\Request;

class RftRequestLog extends Model
{
    protected $guarded = ['id'];

    protected $dates = [
        'request_time'
    ];

    public function responses()
    {
        return $this->hasMany(RftResponseLog::class, 'uuid', 'uuid');
    }

    public function getRequestDataAttribute($value)
    {
        return json_decode($value, true);
    }

    public function getCustomAttribute($value)
    {
        $array = json_decode($value, true);
        return $array ?: $value;
    }

    public function createFromRequest(Request $request)
    {
        return $this->create([
            'batch_no' => $request->batch_no,
            'serial_no' => $this->getSerialNoFromRequest($request),
            'service' => $request->service,
            'uuid' => $request->uuid,
            'client' => $request->client,
            'version' => $request->version,
            'custom' => $request->custom,
            'request_time' => Carbon::createFromTimestamp($request->timestamp),
            'request_data' => $request->toJson(),
        ]);
    }

    public function getSerialNoFromRequest($request)
    {
        return $request->out_serial_no ?: ($request->order_no ?: null);
    }
}
