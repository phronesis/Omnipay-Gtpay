<?php
namespace Omnipay\Gtpay\Message;


use Omnipay\Gtpay\Exception\FailedPaymentException;
use Omnipay\Gtpay\Exception\ValidationException;

class ResponseDataValidator {
    /**
     * @var \Omnipay\Common\Message\RequestInterface | AbstractRequest
     */
    private $request;

    private $data;

    private $response;

    public function __construct(CompletePurchaseResponse $response)
    {
        $this->request = $response->getRequest();
        $this->data = $response->getData();
        $this->response = $response;
    }

    public function validateTransactionRef($expectedRef,$returnedRef){
        if(!$expectedRef || !$returnedRef) return false;
        return self::compareStrings($expectedRef,$returnedRef);
    }

    static function compareStrings($one,$two){
        return strnatcasecmp($one,$two) === 0;
    }

    public function verifyHashValue($gatewayHash,$expectedHash){
        return  self::compareStrings($gatewayHash,$expectedHash);
    }

    /**
     * checks if amount paid is identical to amount due
     * @param float $gatewayAmount Amount returned by the gateway
     * @param float $dueAmount Expected Amount to be returned
     * @return bool
     */
    public function verifyCorrectAmount($gatewayAmount,$dueAmount) {
        $gatewayAmount = floatval($gatewayAmount);
        $dueAmount = floatval($dueAmount);

        return abs($dueAmount - $gatewayAmount)< 0.0001;
    }

    /**
     * @todo move most of this into a validator class
     * @throws FailedPaymentException
     * @throws ValidationException
     */
    public function validate(){
        $statusCode = $this->data['gtpay_tranx_status_code'];

        if(!$this->validateTransactionRef($this->request->getTransactionId(),$this->data['gtpay_tranx_id'])){
            throw $this->determineException(sprintf("Invalid Transaction ref: %s",$this->data['gtpay_tranx_id']),$statusCode);
        }

        if(self::compareStrings(CompletePurchaseResponse::CANCELED_GATEWAY_CODE,$statusCode)){
            throw $this->determineException("Customer Cancellation",$statusCode);
        }

        if(!$this->verifyHashValue($this->data['gtpay_full_verification_hash'],$this->getFullVerificationHash($statusCode))){
            $msg = "Data incompatibility reported. Please contact support";
            throw $this->determineException($msg,$statusCode);
        }
        if(!self::compareStrings($this->data['gtpay_cust_id'],$this->request->getCustomerId())){
            $msg = "Received Customer Id: {$this->data['gtpay_cust_id']} does not match expected Customer Id";
            throw $this->determineException($msg,$statusCode);
        }
        if(!self::compareStrings($this->data['site_redirect_url'],$this->request->getNotifyUrl())){
            throw $this->determineException("Redirect Url is wrong.",$statusCode);
        }

        if(isset($this->data['TransactionCurrency'])){
            if(!self::compareStrings($this->data['TransactionCurrency'],$this->request->getCurrency())){
                throw new ValidationException("Transaction currency does not match expected currency.");
            }
        }

        if(!self::compareStrings($this->data['MertID'],$this->request->getMerchantId())){
            throw new ValidationException("Wrong Merchant ID returned.");
        }
        if(!$this->verifyCorrectAmount($this->data['Amount'],$this->request->getAmountInteger())){
            throw new ValidationException(
                sprintf("Incorrect Amount Paid. Expected Amount: %s, Amount Paid: %s",
                    $this->response->formatIntegerAmount($this->request->getAmountInteger()),
                    $this->response->formatIntegerAmount($this->data['Amount']))
            );
        }
    }


    /**
     * Distinguishes between exceptions that have a failed status code from the gateway
     * and exceptions when the status code indicates success. The later may indicate fraud.
     * For validation exception, you may want to consider sending an email to admin as further investigation
     * may be required
     * @param $msg
     * @param $statusCode
     * @return FailedPaymentException|ValidationException
     */
    private function determineException($msg,$statusCode){
        if($this->response->hasSuccessCode($statusCode)){
            return new ValidationException($msg);
        }else{
            return new FailedPaymentException($msg);
        }
    }

    public function getFullVerificationHash($statusCode){
        $rawString = $this->request->getTransactionId().
            $this->request->getAmountInteger().
            $statusCode.
            $this->request->getCurrency().
            $this->request->getHashKey();
        return hash('sha512',$rawString);
    }

}