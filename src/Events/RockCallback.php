<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-01
 * Time: 18:07
 */

namespace Sureyee\LaravelRockFinTech\Events;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Sureyee\RockFinTech\Contracts\ResponseInterface;

class RockCallback
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rtf-create-account');
    }
}