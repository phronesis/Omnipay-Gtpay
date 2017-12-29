<?php
namespace Omnipay\Gtpay\Message;

abstract class AbstractRequest extends \Omnipay\Common\Message\AbstractRequest
{

    const DEMO_WEBSERVICE_URL = "https://gtweb2.gtbank.com/GTPayService/gettransactionstatus.json";
    const LIVE_WEBSERVICE_URL = "https://ibank.gtbank.com/GTPayService/gettransactionstatus.json";

    /**
     * Generates transaction ID. Payment methods with a different need can extend this
     * @return string  Transaction reference delivered
     */
    public static function generateTransactionId()
    {
        return str_pad(time(), 14, '0', STR_PAD_LEFT);
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->getParameter(Data::MERCHANT_ID);
    }

    public function setMerchantId($merchantId)
    {
        return $this->setParameter(Data::MERCHANT_ID, $merchantId);
    }

    /**
     * The value of this parameter will be the merchant-wide unique identifier of the customer.
     * For example, for a student paying for school fees online, this may be the student's School's Registration Number
     * @param $customerId
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setCustomerId($customerId)
    {
        return $this->setParameter(Data::CUSTOMER_ID, $customerId);
    }

    /**
     * @see AbstractRequest::setCustomerId()
     * @return mixed
     */
    public function getCustomerId()
    {
        return $this->getParameter(Data::CUSTOMER_ID) ;
    }

    /**
     * [Optional Parameter]
     * This describes the transaction to the customer.
     * For example, gtpay_tranx_memo = "John Adebisi (REG13762) : 2nd Term School Fees Payment"
     If not sent, "Purchasing from [Business-Name-Of-Merchant]" will be used
     * @param $memo
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setTransactionMemo($memo)
    {
        return $this->setParameter(Data::TRANSACTION_MEMO, $memo);
    }

    /**
     * @return mixed
     * @see AbstractRequest::setTransactionMemo()
     */
    public function getTransactionMemo()
    {
        return $this->getParameter(Data::TRANSACTION_MEMO);
    }

    /**
     * If specified, then customer cannot choose what gateway to use for the transaction.
     * Accepted values are "webpay" or "ibank" (Bank Transfer) only.
     * @param $gateway
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setGatewayName($gateway)
    {
        return $this->setParameter(Data::GATEWAY_NAME, $gateway);
    }

    /**
     * @see AbstractRequest::setGatewayName()
     * @return mixed
     */
    public function getGatewayName()
    {
        return $this->getParameter(Data::GATEWAY_NAME);
    }

    /**
     *  Merchant can store in this the name to be displayed on the payment page for the customer.
     * @param $customerName
     * @return \Omnipay\Common\Message\AbstractRequest Returns a fluent Interface
     */
    public function setCustomerName($customerName)
    {
        return $this->setParameter(Data::CUSTOMER_NAME, $customerName) ;
    }

    /**
     * @see AbstractRequest::getCustomerName()
     * @return mixed
     */
    public function getCustomerName()
    {
        return $this->getParameter(Data::CUSTOMER_NAME);
    }

    public function getTransactionHash()
    {
        //gtpay_mert_id,gtpay_tranx_id,gtpay_tranx_amt,gtpay_tranx_curr,gtpay_cust_id,gtpay_tranx_noti_url,hash

        $rawString = $this->getMerchantId().
            $this->getTransactionId().
            $this->getAmountInteger().
            $this->getCurrencyNumeric().
            $this->getCustomerId().
            $this->getNotifyUrl().
            $this->getHashKey();
        $hash = hash('sha512', $rawString);
        return $hash;
    }

    /**
     * set Gtpay Hash Key Given to Merchant at Setup
     * @param $hashKey
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setHashKey($hashKey)
    {
        return $this->setParameter(Data::HASH_KEY, $hashKey);
    }

    /**
     * @see Gateway::setHashKey()
     * @return mixed
     */
    public function getHashKey()
    {
        return $this->getParameter(Data::HASH_KEY);
    }

    /**
     * @param $value
     * @return \Omnipay\Common\Message\AbstractRequest
     */
    public function setGatewayFirst($value)
    {
        return $this->setParameter(Data::GATEWAY_FIRST, $value);
    }

    /**
     * @return mixed
     */
    public function getGatewayFirst()
    {
        return $this->getParameter(Data::GATEWAY_FIRST);
    }


    public function getWebserviceUrl()
    {
        return $this->getTestMode()?self::DEMO_WEBSERVICE_URL:self::LIVE_WEBSERVICE_URL;
    }
}
