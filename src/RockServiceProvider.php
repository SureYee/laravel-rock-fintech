<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 13:53
 */

namespace Sureyee\LaravelRockFinTech;


use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Sureyee\LaravelRockFinTech\Console\RockDown;
use Sureyee\LaravelRockFinTech\Console\RockUp;
use Sureyee\RockFinTech\Client;
use Sureyee\RockFinTech\Request;

class RockServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('rock', function () {
            $rft_key = Config::get('rock_fin_tech.rft_key');
            $rft_secret = Config::get('rock_fin_tech.rft_secret');
            $rft_org = Config::get('rock_fin_tech.rft_org');
            $pub_key = Config::get('rock_fin_tech.pub_key');
            $pri_key = Config::get('rock_fin_tech.pri_key');
            return new Rock(new Client($rft_key, $rft_secret, $rft_org, $pub_key, $pri_key), new Request());
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/rock_fin_tech.php' => config_path('rock_fin_tech.php')
        ]);

        $this->loadRoutesFrom(__DIR__ . '/routes.php');

        if ($this->app->runningInConsole()) {
            $this->commands([
                RockDown::class,
                RockUp::class,
            ]);
        }

        Request::setEnv(Config::get('app.env'));
    }
}