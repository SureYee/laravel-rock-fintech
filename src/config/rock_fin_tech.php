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

    'money_dispatch_account' => '', // 红包发放账户



];