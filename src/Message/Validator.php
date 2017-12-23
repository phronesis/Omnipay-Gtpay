<?php
namespace Omnipay\Gtpay\Message;


class Validator {

    static function validateTransactionRef($expectedRef,$returnedRef){
        if(!$expectedRef || !$returnedRef) return false;
        return self::compareStrings($expectedRef,$returnedRef);
    }

    static function compareStrings($one,$two){
        return strnatcasecmp($one,$two) === 0;
    }

    static function verifyHashValue($gatewayHash,$expectedHash){
        return  self::compareStrings($gatewayHash,$expectedHash);
    }


    /**
     * checks if amount paid is identical to amount due
     * @param float $gatewayAmount Amount returned by the gateway
     * @param float $dueAmount Expected Amount to be returned
     * @return bool
     */
    static function verifyCorrectAmount($gatewayAmount,$dueAmount) {
        $gatewayAmount = floatval($gatewayAmount);
        $dueAmount = floatval($dueAmount);

        return abs($dueAmount - $gatewayAmount)< 0.0001;
    }


}