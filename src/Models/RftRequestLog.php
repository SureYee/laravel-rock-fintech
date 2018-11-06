<?php

namespace Sureyee\LaravelRockFinTech\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Sureyee\LaravelRockFinTech\Traits\RevokeTrait;
use Sureyee\RockFinTech\Request;

class RftRequestLog extends Model
{
    use RevokeTrait;

    protected $guarded = ['id'];

    protected $dates = [
        'request_time'
    ];

    public function responses()
    {
        return $this->hasMany(RftResponseLog::class, 'uuid', 'uuid');
    }

    public function syncResponse()
    {
        return $this->hasOne(RftResponseLog::class, 'uuid', 'uuid')->sync();
    }

    public function asyncResponse()
    {
        return $this->hasOne(RftResponseLog::class, 'uuid', 'uuid')->async();
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

    /**
     * 查找唯一的request
     *
     * @param $uuid
     * @param array $columns
     * @return RftRequestLog
     */
    public static function findUuid($uuid, $columns = ['*'])
    {
        return (new static())->where('uuid', $uuid)->first($columns);
    }
}
