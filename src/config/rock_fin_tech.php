<?php

return [

    'rft_key' => env('RFT_KEY'),

    'rft_secret' => env('RFT_SECRET'),

    'rft_org' => env('RFT_ORG'),

    'pub_key' => storage_path(env('RFT_PUB_KEY')),
    'pri_key' => storage_path(env('RFT_PRI_KEY')),


    // 异步采用统一的回调地址，通过触发不同的事件来做出相应。
    'callback' => [
        'bank_recharge' => null,
    ],

    'success_url' => [
        'create_account_p' => 'http://example.com',
        'bind_bank_card_p' => 'http://example.com',
        'bank_recharge' => null,
        'recharge_p' => null,
    ],

    'fail_url' => [
        'create_account_p' => 'http://example.com',
        'bind_bank_card_p' => 'http://example.com',
        'recharge_p' => null,
    ],

    'forget_pwd_url' => [
        'bind_bank_card_p' => 'http://example.com',
        'recharge_p' => null,
    ]



];