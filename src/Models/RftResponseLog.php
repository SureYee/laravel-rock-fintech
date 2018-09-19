<?php

namespace Sureyee\LaravelRockFinTech\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Sureyee\LaravelRockFinTech\Responses\AsyncResponse;
use Sureyee\LaravelRockFinTech\Responses\SyncResponse;
use Sureyee\RockFinTech\Response;

class RftResponseLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'response_data' => 'array',
    ];

    protected $dates = [
        'response_time',
    ];

    protected $response = null;

    public function scopeAsync($query)
    {
        return $query->where('type', 'async');
    }

    public function scopeSync($query)
    {
        return $query->where('type', 'sync');
    }
    
    public function request()
    {
        return $this->belongsTo(RftRequestLog::class, 'uuid', 'uuid');
    }

    public function getResponse()
    {
        return $this->response ?? $this->response = new Response($this->response_data);
    }

    public function getCustomAttribute($value)
    {
        $array = json_decode($value, true);
        return $array ?: $value;
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

    /**
     * 查找唯一的response
     * @param $uuid
     * @param array $columns
     * @return RftRequestLog
     */
    public static function findSyncByUuid($uuid, $columns = ['*'])
    {
        return (new static())
            ->where('uuid', $uuid)
            ->where('type', 'sync')
            ->first($columns);
    }

    /**
     * 查找唯一的response
     * @param $uuid
     * @param array $columns
     * @return RftRequestLog
     */
    public static function findAsyncByUuid($uuid, $columns = ['*'])
    {
        return (new static())
            ->where('uuid', $uuid)
            ->where('type', 'async')
            ->first($columns);
    }

    /**
     * 查找唯一的response
     * @param $uuid
     * @param array $columns
     * @return Collection
     */
    public static function findUuid($uuid, $columns = ['*'])
    {
        return (new static())
            ->where('uuid', $uuid)
            ->get($columns);
    }
}
