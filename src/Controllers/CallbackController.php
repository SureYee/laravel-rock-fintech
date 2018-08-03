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
use Sureyee\LaravelRockFinTech\Facades\Rock;
use Sureyee\RockFinTech\Response;


class CallbackController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function callback(Request $request)
    {
        $this->validSign($request->all());

        $event = $this->getEventInstance($request->get('service'), $request);

        dd($event);
    }

    /**
     * @param $service
     * @param Request $request
     * @return mixed
     */
    protected function getEventInstance($service, Request $request)
    {
        $event = config('rock_fin_tech.callback.' . $service);

        return new $event(new Response($request->all()));
    }

    protected function validSign(array $params)
    {
        if (!Rock::validSign($params)) {
            Log::warning('数据验签失败!', $params);
            abort(403);
        }
    }
}