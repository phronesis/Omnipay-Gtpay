<?php
namespace DavidUmoh\GtPay\Message;

class Data {


    const MERCHANT_ID = 'merchant_id';

    const TRANSACTION_ID = 'transaction_id';

    const TRANSACTION_MEMO = 'transaction_memo';

    const TRANSACTION_AMOUNT = 'transaction_amount';

    const TRANSACTION_CURRENCY = 'transaction_currency';

    const NOTIFY_URL = 'notify_url';

    const GATEWAY_FIRST = 'gateway_first';

    const ECHO_DATA = 'echo_data';

    const CUSTOMER_NAME = 'customer_name';

    const CUSTOMER_ID = 'customer_id';

    const GTPAY_HASH = 'gtpay_hash';

    const GATEWAY_NAME = 'gateway_name';

    const HASH_KEY = 'hash_key';


    private $paramMap = [
        'merchant_id'=>'gtpay_mert_id',
        'transaction_id'=>'gtpay_tranx_id',
        'transaction_memo'=>'gtpay_tranx_memo',
        'transaction_amount'=>'gtpay_tranx_amt',
        'transaction_currency'=>'gtpay_tranx_curr',
        'notify_url'=>'gtpay_tranx_noti_url',
        'gateway_first'=>'gtpay_gway_first',
        'echo_data'=>'gtpay_echo_data',
        'customer_name'=>'gtpay_cust_name',
        'customer_id'=>'gtpay_cust_id',
        'hash'=>'gtpay_hash',
        'gateway_name'=>'gtpay_gway_name',
        'hash_key'=>'hash_key'
    ];


    public function getParamMatch(){
        return $this->paramMap;
    }

}

