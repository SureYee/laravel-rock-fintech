# laravel钜石接口API

#### 项目介绍
rock-fintech的laravel封装


#### 安装说明

1. 在`config/app.php`中添加服务

    ```php
   'providers' => [
       ...
       \Sureyee\LaravelRockFinTech\RockServiceProvider::class,
   ]
    ```

    ```php
    'aliases' => [
       ...
       'Rock' => Sureyee\LaravelRockFinTech\Facades\Rock::class,
    ]
    ```
    
2. 运行 `php artisan vendor:publish` 发布配置项文件

#### 使用说明

所有的接口均使用API接口中的`service`名称的小驼峰形式命名，可以通过`Facade`门面直接调用。

1. 调用接口：

    ```php
       // 注册账户
       $mobile = '18666666666';
       $response = Rock::createAccountP($mobile)->send();
       // 同步回调
       if ($response->isSuccess()) {
           // do...sth...
       }  else {
           // notify wrong things
       }
    ```
    
2. 处理异步回调

    异步回调均通过事件解耦，可以在config文件中自定义回调触发事件，然后通过事件订阅者进行执行。

    ```php
   // config
   'callback' => [
       'create_account_p' =>  \Sureyee\LaravelRockFinTech\Events\CreateAccountCallback::class,
   ],
    // EventServiceProvider.php
   protected $listen = [
       BatchRepaymentCallback::class => [
           TestListener::class
       ]
   ];

    // listener
   namespace App\Listeners;
   
   use Illuminate\Queue\InteractsWithQueue;
   use Illuminate\Contracts\Queue\ShouldQueue;
   use Sureyee\LaravelRockFinTech\Exceptions\EventFailedException;
   
   class TestListener
   {
       /**
        * Create the event listener.
        *
        * @return void
        */
       public function __construct()
       {
           //
       }
   
       /**
        * Handle the event.
        *
        * @param  object  $event
        * @return void
        */
       public function handle($event)
       {
           // 执行业务逻辑
        
           // 失败抛出 EventFailed 错误即可
           throw new EventFailedException('failed');
       }
   }
    ```
    如果是推送到队列进行处理，抛出错误是没有办法被捕捉的。
    并且会直接输出`success`
    
3. 系统维护

    如果接口系统进入维护可以在`artisan`中执行命令来关闭接口的请求，**该功能不会影响业务的回调操作**。
    
    `php artisan rock:down` 命令会让系统进入维护状态，所有接口请求均会抛出`SystemDownException`错误。
    
    捕捉错误后可以通过`Rock::getSystemMaintenanceTime()` 方法获取系统维护的开始时间和结束时间。
    
    命令提供`--start`参数指定系统维护开始时间，如：`php artisan rock:down --start=tomorrow` 则明天开始进行维护
    
    命令提供`--h` 参数指定系统维护时长，单位是小时，如：`php artisan rock:down --h=2` 现在开始进入维护，维护时间2小时，2小时后自动开启服务。
    
    `php artisan rock:up` 手动启动系统服务

4. 事件

     `RockCallback` 事件。 `RockCallback` 事件用于处理接口的异步回调，如果没有在配置项中指定 `service` 的回调事件，则默认触发该事件。
     
     `RockBeforeRequest` 事件，会在请求之前触发，通过 `$event->request` 可以获得请求的 `Request` 对象。
     
     `RockAfterRequest` 事件，会在同步回调完成之后触发，通过 `$event->request` 可以获得 `Request` 对象， `$event->response` 可以获得同步回调的 `Response` 对象。
    
5. 更新记录

    | 版本号 | 更新内容 |
    |:-------:|-------|
    | v1.1.0|修改接口的访问方式|
    