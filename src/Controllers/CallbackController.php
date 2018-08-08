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
use Sureyee\LaravelRockFinTech\Events\RockCallback;
use Sureyee\LaravelRockFinTech\Exceptions\ConfigSettingErrorException;
use Sureyee\LaravelRockFinTech\Facades\Rock;
use Sureyee\RockFinTech\Response;


class CallbackController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param Request $request
     * @return string
     */
    public function callback(Request $request)
    {
        Log::debug('rock-fin-tech callback:', $request->all());
        $this->validSign($request->all());

        try {
            $event = $this->getEventInstance($request->get('service'), $request);

            event($event);

        } catch (ConfigSettingErrorException $exception) {
            Log::critical($exception->getMessage());
            return 'failed';
        } catch (EventFailedException $exception) {
            Log::error($exception->getMessage());
            return 'failed';
        }
        return 'success';
    }

    /**
     * @param $service
     * @param Request $request
     * @return mixed
     * @throws
     */
    protected function getEventInstance($service, Request $request)
    {
        $event = config('rock_fin_tech.callback.' . $service);

        if (is_null($event)) {
            Log::debug('rock-fin-tech trigger event:RockCallback');
            return new RockCallback(new Response($request->all()));
        }

        if (class_exists($event)) {
            Log::debug('rock-fin-tech trigger event:' . $event);
            return new $event(new Response($request->all()));
        }
        throw new ConfigSettingErrorException('rock_fin_tech.callback.' . $service . '配置错误！');
    }

    protected function validSign(array $params)
    {
        if (!Rock::validSign($params)) {
            Log::warning('数据验签失败!', $params);
            abort(403, '数据验签失败');
        }
    }
}