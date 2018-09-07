<?php

namespace Sureyee\LaravelRockFinTech\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Sureyee\RockFinTech\Request;

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
}
