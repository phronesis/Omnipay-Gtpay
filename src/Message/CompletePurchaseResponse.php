<?php
namespace Omnipay\Gtpay\Message;


use Omnipay\Common\Message\AbstractResponse;

class CompletePurchaseResponse extends AbstractResponse{

    public function isSuccessful()
    {
        return true;
    }

    public function getTransactionReference()
    {
        return $this->data['MerchantReference'];
    }

    public function getTransactionId()
    {
        return $this->data['gtpay_tranx_id'];
    }


}