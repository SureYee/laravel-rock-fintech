<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-08
 * Time: 16:30
 */

namespace Sureyee\LaravelRockFinTech\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Sureyee\RockFinTech\Request;


class RockBeforeRequest
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rtf-create-account');
    }
}