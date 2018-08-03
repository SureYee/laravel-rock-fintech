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
       $response = Rock::createAccountP($mobile);
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
   use Sureyee\LaravelRockFinTech\Controllers\EventFailedException;
   
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
    