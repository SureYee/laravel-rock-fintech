<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-07-31
 * Time: 13:53
 */

namespace Sureyee\LaravelRockFinTech;


use Illuminate\Support\ServiceProvider;
use Sureyee\RockFinTech\Request;

class RockServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton('rock', function () {
           return new Rock(new Request());
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/rock_fin_tech.php'
        ]);
    }
}