<?php

namespace Sureyee\LaravelRockFinTech\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Sureyee\LaravelRockFinTech\Responses\AsyncResponse;
use Sureyee\LaravelRockFinTech\Responses\SyncResponse;
use Sureyee\RockFinTech\Response;

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

    /**
     *
     * @param AsyncResponse $response
     * @return RftRequestLog
     */
    public function createFromResponse(Response $response)
    {
        return $this->create([
            'type' => $response->getType(),
            'code' => $response->getCode(),
            'msg' => $response->getMessage(),
            'service' => $response->service,
            'uuid' => $response->uuid,
            'version' => $response->version,
            'response_time' => Carbon::createFromTimestamp($response->timestamp),
            'custom' => $response->custom,
            'response_data' => json_encode($response->toArray()),
            'sequence_id' => $response->sequence_id
        ]);
    }
}
