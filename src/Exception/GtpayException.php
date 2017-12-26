<?php
namespace Omnipay\Gtpay\Exception;

use Throwable;

class GtpayException extends \Exception{

    private $responseDump;

    public function __construct($message = "", $code = 0, Throwable $previous = null,$responseDump)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getResponseDump(){
        return $this->responseDump;
    }

}