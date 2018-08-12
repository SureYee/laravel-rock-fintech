<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-12
 * Time: 10:30
 */

namespace Sureyee\LaravelRockFinTech\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Sureyee\RockFinTech\Request;
use Sureyee\RockFinTech\Response;


class RockAfterRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    public $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rtf-before-request');
    }
}