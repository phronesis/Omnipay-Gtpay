<?php
namespace Omnipay\Gtpay\Message;

class PurchaseRequest extends AbstractRequest
{

    public function getData()
    {

        $data = [
            "gtpay_cust_id"=>$this->getCustomerId(),
            "gtpay_cust_name"=>$this->getCustomerName(),
            "gtpay_mert_id"=>$this->getMerchantId(),
            "gtpay_hash" => $this->getTransactionHash(),
            "gtpay_tranx_id" =>$this->getTransactionId(),
            "gtpay_tranx_noti_url" => $this->getNotifyUrl(),
            "gtpay_tranx_amt" => $this->getAmountInteger(),
            "gtpay_tranx_curr" => $this->getCurrencyNumeric(),

        ];

        if ($this->getGatewayName()) {
            $data["gtpay_gway_name"] = $this->getGatewayName();
        }

        return $data;
    }


    public function sendData($data)
    {
        return new PurchaseResponse($this, $data);
    }
}
