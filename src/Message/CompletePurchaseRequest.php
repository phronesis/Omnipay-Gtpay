<?php
namespace Omnipay\Gtpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Gtpay\Exception\FailedPaymentException;
use Omnipay\Gtpay\Exception\ValidationException;

class CompletePurchaseRequest extends AbstractRequest{

    const CANCELED_GATEWAY_CODE = 'Z6';

    const SUCCESS_CODE = '00';

    private $responseBody;
    public function getData()
    {
        $requestBody = $this->httpRequest->request->all();
        $this->responseBody = $requestBody;
        if(empty($requestBody)){
            throw new InvalidRequestException('No Request Body Found');
        }

        $this->preWebserviceValidation($requestBody);
        $gatewayResponse = $this->queryGateway();
        $this->responseBody = $this->mergeResponse($gatewayResponse);
        $this->postWebserviceValidation($gatewayResponse);
        $this->setTransactionReference($gatewayResponse['MerchantReference']);
        return $this->responseBody;
    }

    public function sendData($data)
    {
        return new CompletePurchaseResponse($this,$data);
    }


    /**
     * @param $data
     * @throws FailedPaymentException
     * @throws ValidationException
     */
    private function preWebserviceValidation($data){
        $statusCode = $data['gtpay_tranx_status_code'];

        if(!Validator::validateTransactionRef($this->getTransactionId(),$data['gtpay_tranx_id'])){
            throw $this->determineException(sprintf("Invalid Transaction ref: %s",$data['gtpay_tranx_id']),$statusCode);
        }

        if(Validator::compareStrings(self::CANCELED_GATEWAY_CODE,$statusCode)){
            throw $this->determineException("Customer Cancellation",$statusCode);
        }

        if(!Validator::verifyHashValue($data['gtpay_full_verification_hash'],$this->getFullVerificationHash($statusCode))){
            $msg = "Data incompatibility reported. Please contact support";
            throw $this->determineException($msg,$statusCode);
        }
        if(!Validator::compareStrings($data['gtpay_cust_id'],$this->getCustomerId())){
            $msg = "Received Customer Id: {$data['gtpay_cust_id']} does not match expected Customer Id";
            throw $this->determineException($msg,$statusCode);
        }
        if(!Validator::compareStrings($data['site_redirect_url'],$this->getNotifyUrl())){
            throw $this->determineException("Redirect Url is wrong.",$statusCode);
        }
    }

    protected function postWebserviceValidation($data){

        if(isset($data['TransactionCurrency'])){
            if(!Validator::compareStrings($data['TransactionCurrency'],$this->getCurrency())){
                throw new ValidationException("Transaction currency does not match expected currency.",
                    0,null,$this->responseBody);
            }
        }

        if(!Validator::compareStrings($data['MertID'],$this->getMerchantId())){
            throw new ValidationException("Wrong Merchant ID returned.",0,null,$this->responseBody);
        }
        if(!Validator::verifyCorrectAmount($data['Amount'],$this->getAmountInteger())){
            throw new ValidationException(
                sprintf("Incorrect Amount Paid. Expected Amount: %s, Amount Paid: %s",
                    $this->formatIntegerAmount($this->getAmountInteger()),
                    $this->formatIntegerAmount($data['Amount'])),
                0,null,$this->responseBody
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
        if($this->hasSuccessCode($statusCode)){
            return new ValidationException($msg,0,null,$this->responseBody);
        }else{
            return new FailedPaymentException($msg,0,null,$this->responseBody);
        }
    }


    private function hasSuccessCode($statusCode){
        return Validator::compareStrings($statusCode,self::SUCCESS_CODE);
    }

    public function getFullVerificationHash($statusCode){
        $rawString = $this->getTransactionId().
            $this->getAmountInteger().
            $statusCode.
            $this->getCurrency().
            $this->getHashKey();
        return hash('sha512',$rawString);
    }

    public function queryGateway(){
        $param = [
            'tranxid' => $this->getTransactionId(),
            'amount' => $this->getAmountInteger(),
            'mertid' => $this->getMerchantId(),
            'hash'=>$this->getVerificationHash()
        ];
        $response = $this->httpClient->get($this->getWebserviceUrl(),null,
            ['query'=>$param,'read_timeout'=>60])->send();

        if($response->getStatusCode() !== 200){
            throw new InvalidResponseException();
        }
        $body = (string) $response->getBody();
        return json_decode($body,true);
    }

    private function getVerificationHash(){
        $hashString = $this->getMerchantId() . $this->getTransactionId() . $this->getHashKey();
        return hash('sha512',$hashString);
    }

    public function formatIntegerAmount($integerAmount){
        return $this->getCurrency().' '.number_format($this->convertIntegerAmount($integerAmount),$this->getCurrencyDecimalPlaces());
    }

    public function convertIntegerAmount($integerAmount){
        return $integerAmount/$this->getCurrencyDecimalFactor();
    }

    private function getCurrencyDecimalFactor()
    {
        return pow(10, $this->getCurrencyDecimalPlaces());
    }

    private function mergeResponse($newResponse){
        $responseBody = $this->responseBody;
        if(empty($responseBody)) return $newResponse;
        return array_merge($responseBody,$newResponse);

    }
}