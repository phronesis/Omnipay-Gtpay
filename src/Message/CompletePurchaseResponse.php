<?php
namespace Omnipay\Gtpay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

class CompletePurchaseResponse extends AbstractResponse
{

    const CANCELED_GATEWAY_CODE = 'Z6';

    const SUCCESS_CODE = '00';

    private $isSuccessful = null;

    private $validator;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);
        $this->validator = new ResponseDataValidator($this);
    }

    public function isSuccessful()
    {
        if (is_null($this->isSuccessful)) {
            $this->validator->validate();
            $this->isSuccessful = $this->checkSuccessStatus();
            if ($this->isSuccessful()) {
                $this->validator->successValidate();
            }
        }
        return $this->isSuccessful;
    }

    private function checkSuccessStatus()
    {
        return $this->hasSuccessCode($this->getCode());
    }

    public function getTransactionReference()
    {
        return isset($this->data['MerchantReference'])?$this->data['MerchantReference']:null;
    }

    public function getTransactionId()
    {
        return isset($this->data['gtpay_tranx_id'])?$this->data['gtpay_tranx_id']:null;
    }

    public function getMessage()
    {
        if (!isset($this->data['gtpay_tranx_status_msg'])) {
            return null;
        }
        return isset($this->data['ResponseDescription'])
            ?$this->data['ResponseDescription']: $this->data['gtpay_tranx_status_msg'];
    }

    public function getCode()
    {
        if (!isset($this->data['gtpay_tranx_status_code'])) {
            return null;
        }
        return isset($this->data['ResponseCode'])?$this->data['ResponseCode']:$this->data['gtpay_tranx_status_code'];
    }

    public function getGatewayAmount()
    {
        return isset($this->data['gtpay_tranx_amt'])? $this->data['gtpay_tranx_amt']:null;
    }

    public function getGatewayAmountInteger()
    {
        return isset($this->data['gtpay_tranx_amt_small_denom'])?$this->data['gtpay_tranx_amt_small_denom']:null;
    }

    public function getApprovedAmount()
    {
        return isset($this->data['Amount'])?$this->data['Amount']:0;
    }

    /**
     * Get currency returned from gateway
     * @return null
     */
    public function getGatewayNumericCurrency()
    {
        return isset($this->data['gtpay_tranx_curr'])?$this->data['gtpay_tranx_curr']:null;
    }

    public function formatIntegerAmount($integerAmount)
    {
        return $this->getRequest()->getCurrency().' '.number_format($this->convertIntegerAmount($integerAmount), $this->getRequest()->getCurrencyDecimalPlaces());
    }

    public function convertIntegerAmount($integerAmount)
    {
        return $integerAmount/$this->getCurrencyDecimalFactor();
    }

    public function getCurrencyDecimalFactor()
    {
        return pow(10, $this->getRequest()->getCurrencyDecimalPlaces());
    }

    public function hasSuccessCode($statusCode)
    {
        return ResponseDataValidator::compareStrings($statusCode, self::SUCCESS_CODE);
    }
}
