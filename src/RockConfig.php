<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-01
 * Time: 15:42
 */

namespace Sureyee\LaravelRockFinTech;


class RockConfig
{
    // 账户类型 普通户 企业户
    const ACCOUNT_TYPE_COMMON = 200201;
    const ACCOUNT_TYPE_COMPANY = 200204;

    // 用户角色 出借角色 借款角色 代偿角色
    const ROLE_TYPE_LENDER = 1;
    const ROLE_TYPE_BORROWER = 2;
    const ROLE_TYPE_UNDERWRITER = 3;
}