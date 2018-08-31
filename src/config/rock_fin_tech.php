<?php

return [

    'rft_key' => env('RFT_KEY'),

    'rft_secret' => env('RFT_SECRET'),

    'rft_org' => env('RFT_ORG'),

    'pub_key' => storage_path(env('RFT_PUB_KEY')),
    'pri_key' => storage_path(env('RFT_PRI_KEY')),


    // 异步采用统一的回调地址，通过触发不同的事件来做出相应。
    'callback' => [

    ],

    'success_url' => [
        'default' => ''
    ],

    'fail_url' => [
        'default' => ''
    ],

    'forget_pwd_url' => [
        'default' => ''
    ],

    'assure_account' =>  env('RFT_ASSURE_ACCOUNT'), // 担保账户

    'money_dispatch_account' =>  env('RFT_MONEY_DISPATCH_ACCOUNT'), // 红包发放账户

    'sftp' => [
        'host' => env('RFT_SFTP_HOST'),
        'port' => env('RFT_SFTP_PORT'),
        'username' => env('RFT_SFTP_USERNAME'),
        'password' => env('RFT_SFTP_PASSWORD'),
        'timeout' => 10,
    ],

    'sftp_origin' => env('RFT_SFTP_ORIGIN'),

];