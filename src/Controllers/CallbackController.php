<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-01
 * Time: 18:13
 */

namespace Sureyee\LaravelRockFinTech\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Log;


class CallbackController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callback(Request $request)
    {
        Log::info('request data:', $request->all());
//        $this->getEventInstance();
    }

    protected function getEventInstance()
    {
        $event = config('rock_fin_tech.callback.');
    }
}