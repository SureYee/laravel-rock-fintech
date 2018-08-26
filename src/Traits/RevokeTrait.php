<?php
/**
 * Created by PhpStorm.
 * User: sure
 * Date: 2018-08-25
 * Time: 9:35
 */

namespace Sureyee\LaravelRockFinTech\Traits;


use Sureyee\LaravelRockFinTech\Exceptions\CantRevokedServiceException;
use Sureyee\LaravelRockFinTech\Facades\Rock;
use Sureyee\RockFinTech\Contracts\ResponseInterface;

trait RevokeTrait
{
    use RequestTrait;

    private $revokeSetting = [
        'frozen' => 'frozen',
        'sign_borrower_p' => [
            'repayment' => 'repayment',
            'payment' => 'payment'
        ]
    ];

    /**
     * @param null|string $revokeService
     * @return ResponseInterface
     * @throws CantRevokedServiceException
     */
    public function revoke($revokeService = null):ResponseInterface
    {
        $service = $this->getService();

        if ($this->canRevoke()) {
            $setting = $this->revokeSetting[$service];
            $method = $this->getMethodFromSetting($setting, $revokeService);
            return $this->$method();
        }
        throw new CantRevokedServiceException($service);
    }

    /**
     * @param $setting
     * @param $revokeService
     * @return string
     * @throws CantRevokedServiceException
     */
    protected function getMethodFromSetting($setting, $revokeService)
    {
        $prefix = 'revoke_';
        if (is_array($setting) && array_key_exists($revokeService, $setting))
            return $prefix . $setting[$revokeService];

        if (is_string($setting)) return $prefix . $setting;

        throw new CantRevokedServiceException($revokeService);
    }

    /**
     * @return bool
     */
    public function canRevoke()
    {
        return array_key_exists($this->getService(), $this->revokeSetting);
    }

    /**
     * @return ResponseInterface
     */
    protected function revokeFrozen():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::unfrozen($request->card_no, $request->amount, $this->getSerialNo());
    }

    /**
     * @return ResponseInterface
     */
    protected function revokeRepayment():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeRepayment($request->card_no, $this->getSerialNo());
    }

    /**
     * 撤销放款手续费签约
     * @return ResponseInterface
     */
    protected function revokePayment():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokePayment($request->card_no, $this->getSerialNo());
    }

    protected function revokeTransfer()
    {

    }
}