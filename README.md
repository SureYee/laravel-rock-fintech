# laravel钜石接口API

#### 项目介绍

rock-fintech的laravel封装，加入事件，控制台等机制。**dev-master为开发包，请谨慎使用**


#### 安装说明

1. 安装接口包
    
    `composer require sureyee/laravel-rock-fentech`
    
2. 运行 `php artisan vendor:publish` 发布配置项文件

#### 使用说明

所有的接口均使用API接口中的`service`名称的小驼峰形式命名，可以通过`Facade`门面直接调用。

1. 调用接口：

    1. 非批次调用
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
        
    2. 批次调用
    
        批次调用首先要创建一个`Transformer`类，实现`TransformerInterface`接口。
        
       ```php
        namespace App\Transformers;
        
        use Sureyee\LaravelRockFinTech\Contracts\TransformerInterface;
        use Sureyee\RockFinTech\RockConfig;
        
        class RepayTransformer implements TransformerInterface
        {
        
            public function __construct()
            {

            }
        
            public function format($incomeRecord): array
            {
                return [
                    'out_card_no' => $incomeRecord->out_card_no,
                    'amount' => $incomeRecord->amount,
                    'interest_amount' => $incomeRecord->interest_amount,
                    'in_card_no' => $incomeRecord->in_card_no,
                    'currency' => RockConfig::CNY,
                    'out_fee_mode' => 0,
                    'out_fee_amount' => 0,
                    'in_fee_mode' => 0,
                    'in_fee_amount' => 0,
                    'assets_no' => $incomeRecord->asset_no,
                    'auth_code' => $incomeRecord->auth_code,
                    'serial_no' => $incomeRecord->serial_no,
                    'third_reserved' => '',
                    'penalty_interest_amount' => 0,
                    'reserved' => $this->reserved($incomeRecord),
                ];
            }

            /**
             * 自定义参数
             * @param IncomeRecord $incomeRecord
             * @return string
             */
            protected function reserved(IncomeRecord $incomeRecord)
            {
                return json_encode([
                    'income_record' => $incomeRecord->id
                ]);
            }
        }  
       ```
       
       接口调用
       
       ```php
        $itemsRequest = new ItemsRequest($collect, new RepayTransformer);
        
        $response = Rock::batchRepaymentB($itemsRequest)->send();
        ```     
    
    3. 添加`custom`参数
    
        `Rock::createAccountP()->custom(['one' => 1])->send()`
        
        `custom` 方法接收一个`string`或者`array`用作自定义参数。数组会在放入`Request`前转换为`json`字符串.
       
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
    
    命令提供`start`参数指定系统维护开始时间，如：`php artisan rock:down tomorrow` 则明天开始进行维护
    
    命令提供`--h` 选项指定系统维护时长，单位是小时，如：`php artisan rock:down --h=2` 现在开始进入维护，维护时间2小时，2小时后自动开启服务。
    
    `php artisan rock:up` 手动启动系统服务

4. 事件

     `RockCallback` 事件。 `RockCallback` 事件用于处理接口的异步回调，如果没有在配置项中指定 `service` 的回调事件，则默认触发该事件。
     
     `RockBeforeRequest` 事件，会在请求之前触发，通过 `$event->request` 可以获得请求的 `Request` 对象。
     
     `RockAfterRequest` 事件，会在同步回调完成之后触发，通过 `$event->request` 可以获得 `Request` 对象， `$event->response` 可以获得同步回调的 `Response` 对象。
    
