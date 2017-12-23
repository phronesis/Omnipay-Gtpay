<?php
namespace Omnipay\Gtpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Gtpay\Exception\FailedPaymentException;
use Omnipay\Gtpay\Exception\ValidationException;

class CompletePurchaseRequest extends AbstractRequest{

    const CANCELED_GATEWAY_CODE = 'Z6';

    const SUCCESS_CODE = '00';

    public function getData()
    {
        $requestBody = $this->httpRequest->request->all();
        if(empty($requestBody)){
            throw new InvalidRequestException('No Request Body Found');
        }


        //pre-webservice validation
        $this->preWebserviceValidation();
        //remotely check payment status


        //post -webservice validation
        //if successfully, run checks
         //-check hash, check amount, correct amount
    }

    public function sendData($data)
    {

    }

    private function preWebserviceValidation($data){
        $statusCode = $data['gtpay_tranx_status_code'];
        if(!Validator::compareStrings(self::CANCELED_GATEWAY_CODE,$statusCode)){
            throw $this->determineException("Customer Cancellation",$statusCode);
        }
        if(!Validator::verifyHashValue($data['gtpay_full_verification_hash'],$this->getTransactionHash())){
            $msg = "Data incompatibility reported. Please contact support";
            throw $this->determineException($msg,$statusCode);
        }
        if(!Validator::compareStrings($data['gtpay_cust_id'],$this->getCustomerId())){
            $msg = "Received Customer Id: {$data['gtpay_cust_id']} does not match expected Customer Id";
            throw $this->determineException($msg,$statusCode);
        }
        if(!$this->validation->compareStrings($data['site_redirect_url'],$this->getReturnUrl())){
            $this->determineException("Redirect Url is wrong.",$statusCode);
        }
    }

    private function determineException($msg,$statusCode){
        if($this->hasSuccessCode($statusCode)){
            return new ValidationException($msg);
        }else{
            return new FailedPaymentException($msg);
        }
    }

    private function hasSuccessCode($statusCode){
        return Validator::compareStrings($statusCode,self::SUCCESS_CODE);
    }

    private function queryGateway(){

    }
}