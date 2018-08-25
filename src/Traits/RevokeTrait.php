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

    private $canRevokedServices = [
        'frozen',
        ''
    ];

    /**
     * @return ResponseInterface
     * @throws CantRevokedServiceException
     */
    public function revoke():ResponseInterface
    {
        $service = $this->getService();

        if (in_array($service, $this->canRevokedServices)) {
            $method = camel_case('revoke_' . $service);
            return $this->$method();
        }
        throw new CantRevokedServiceException($service);
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