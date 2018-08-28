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
use Sureyee\RockFinTech\Request;
use Sureyee\RockFinTech\Response;

trait RevokeTrait
{
    use RequestTrait;

    private $revokeSetting = [
        'frozen' => 'frozen',
        'sign_borrower_p' => [
            'repayment' => 'repayment',
            'payment' => 'payment'
        ],
        'sign_transfer_p' => 'transfer',
        'bid_apply_p' => 'bid',
        'sign_auto_bid_p' => 'auto_bid',
        'trustee_pay_p' => 'trustee_pay',
        'sign_credit_transfer_p' => 'credit_transfer',
        'sign_warrant_p' => 'warrant',
        'money_dispatch' => 'money',
        'batch_buy_credit_b' => 'buy_credit_batch'
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

    /**
     * 撤销转让方转让手续费签约
     * @return ResponseInterface
     */
    protected function revokeTransfer():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeTransfer($request->card_no, $this->getSerialNo());
    }

    /**
     * 借款人标的撤销
     * @return ResponseInterface
     */
    protected function revokeAsset():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::assetsRevoke($request->asset_no, $request->card_no, $request->amount, $this->getThirdCustom());
    }

    /**
     * 投标申请撤销
     * @return ResponseInterface
     */
    protected function revokeBid():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeBid($request->card_no, $this->getSerialNo(), $request->amount, $request->asset_no);
    }

    /**
     * 撤销自动投标签约
     * @return ResponseInterface
     */
    protected function revokeAutoBid():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeAutoBid($request->card_no, $this->getSerialNo());
    }

    /**
     * 受托支付撤销
     * @return ResponseInterface
     */
    protected function revokeTrusteePay():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeTrusteePay($request->card_no, $request->debt_card_no);
    }

    /**
     * 撤销自动债权转让签约
     * @return ResponseInterface
     */
    protected function revokeCreditTransfer():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeCreditTransfer($request->card_no, $this->getSerialNo(), $this->getThirdCustom());
    }

    /**
     * 撤销借款人还款担保人金额签约
     * @return ResponseInterface
     */
    protected function revokeWarrant():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::revokeWarrant($request->card_no, $this->getSerialNo());
    }

    /**
     * 红包发放撤销
     * @return ResponseInterface
     */
    protected function revokeMoney():ResponseInterface
    {
        $request = $this->getRequestData();

        return Rock::moneyRevoke(
            $request->timestamp,
            $request->out_serial_no,
            $request->card_no_in,
            $request->amount,
            $request->card_no,
            $request->currency,
            $description = $this->getDescription()
        );
    }
}