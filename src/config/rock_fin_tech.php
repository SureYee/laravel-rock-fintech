<?php

return [

    'rft_key' => env('RFT_KEY'),

    'rft_secret' => env('RFT_SECRET'),

    'rft_org' => env('RFT_ORG'),

    'pub_key' => storage_path(env('RFT_PUB_KEY')),
    'pri_key' => storage_path(env('RFT_PRI_KEY')),


    // 异步采用统一的回调地址，通过触发不同的事件来做出相应。
    'callback' => [
        'create_account_p' => \Sureyee\LaravelRockFinTech\Events\CreateAccountCallback::class,
        'batch_repayment_b' => \Sureyee\LaravelRockFinTech\Events\BatchRepaymentCallback::class,
    ],

    'success_url' => [
        'create_account_p' => 'http://example.com',
        'bind_bank_card_p' => 'http://example.com',
    ],

    'fail_url' => [
        'create_account_p' => 'http://example.com',
        'bind_bank_card_p' => 'http://example.com',
    ],

    'forget_pwd_url' => [
        'bind_bank_card_p' => 'http://example.com',
    ]



];