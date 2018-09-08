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

    protected $casts = [
        'response_data' => 'array',
        'custom' => 'array',
    ];

    protected $dates = [
        'response_time',
    ];

    protected $response = null;

    public function request()
    {
        return $this->belongsTo(RftRequestLog::class, 'uuid', 'uuid');
    }

    public function getResponse()
    {
        return $this->response ?? $this->response = new Response($this->response_data);
    }

    /**
     *
     * @param Response|AsyncResponse|SyncResponse $response
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
            'response_data' => $response->toArray(),
            'sequence_id' => $response->sequence_id
        ]);
    }
}
