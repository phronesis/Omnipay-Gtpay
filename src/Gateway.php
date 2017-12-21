<?php

namespace Omnipay\Gtpay;

use Omnipay\Gtpay\Message\Data;
use \Omnipay\Common\AbstractGateway;

class Gateway extends AbstractGateway{

    const GATEWAY_NAME = "Gtpay";

    const GATEWAY_WEBPAY = 'webpay';

    const GATEWAY_BANK = 'ibank';

    // - demo

    public function getName()
    {
       return self::GATEWAY_NAME;
    }

    /**
     * Set Merchant ID parameter as required by Gtpay.
     * This is the GTPay-wide unique identifier of merchant, assigned by GTPay and communicated to merchant by GTBank
     * @param $merchantId
     * @return $this
     */
    public function setMerchantId($merchantId){
        return $this->setParameter(Data::MERCHANT_ID,$merchantId);
    }

    /**
     * set Gtpay Hash Key Given to Merchant at Setup
     * @param $hashKey
     * @return $this
     */
    public function setHashKey($hashKey){
        return $this->setParameter(Data::HASH_KEY,$hashKey);
    }

    /**
     * @see Gateway::setHashKey()
     * @return mixed
     */
    public function getHashKey(){
        return $this->getParameter(Data::HASH_KEY);
    }

    public function getDefaultParameters()
    {
        return [
            Data::MERCHANT_ID => '',
            Data::HASH_KEY => '',
            'testMode' => true,
            Data::GATEWAY_FIRST=>'no',
            Data::GATEWAY_NAME=>self::GATEWAY_WEBPAY
            ];
    }

    /**
     * @see Gateway::setMerchantId()
     * @return mixed
     */
    public function getMerchantId(){
        return $this->getParameter(Data::MERCHANT_ID);
    }


    /**
     *
     * If specified, then customer cannot choose what gateway to use for the transaction.
     * Accepted values are "webpay" or "ibank" (Bank Transfer) only.
     * @param $gateway
     * @return $this
     */
    public function setGatewayName($gateway){
        return $this->setParameter(Data::GATEWAY_NAME,$gateway);
    }

    /**
     * @see Gateway::setGatewayName()
     * @return mixed
     */
    public function getGatewayName(){
        return $this->getParameter(Data::GATEWAY_NAME);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setGatewayFirst($value){
        return $this->setParameter(Data::GATEWAY_FIRST,$value);
    }

    public function getGatewayFirst(){
        return $this->getParameter(Data::GATEWAY_FIRST);
    }


    /**
     * @param array $parameters
     * @return \Omnipay\Common\Message\AbstractRequest|\Omnipay\Common\Message\RequestInterface
     */
    public function purchase(array $parameters = array())
    {
        return $this->createRequest('\Omnipay\Gtpay\Message\PurchaseRequest',$parameters);
    }


    public function completePurchase(array $options = array())
    {
        return $this->createRequest('\Omnipay\Gtpay\Message\CompletePurchaseRequest',$options);
    }

    /**
     * Generates transaction ID. Payment methods with a different need can extend this
     * @return string  Transaction reference delivered
     */
    public function generateTransactionId() {
        return str_pad(time(), 14, '0', STR_PAD_LEFT);
    }


}