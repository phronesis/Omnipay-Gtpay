<?php
namespace DavidUmoh\Gtpay\Tests;

use DavidUmoh\Gtpay\Gateway;
use DavidUmoh\Gtpay\Message\Data;
use DavidUmoh\Gtpay\Message\PurchaseRequest;
use DavidUmoh\Gtpay\Message\PurchaseResponse;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase{

    public $gateway;

    public $options;

    const HASH_KEY = 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F';
    private $transactionId = '00001513866984';
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('212');
        $this->gateway->setHashKey(self::HASH_KEY);
        $this->gateway->setGatewayFirst('no');
        $this->gateway->setGatewayName(Gateway::GATEWAY_BANK);
        $this->gateway->setCurrency('NGN');
    }

    public function testGateway(){

        $this->assertEquals('212',$this->gateway->getMerchantId());
        $this->assertEquals(self::HASH_KEY,$this->gateway->getHashKey());
        $this->assertEquals(Gateway::GATEWAY_BANK,$this->gateway->getGatewayName());
        $this->assertEquals('no',$this->gateway->getGatewayFirst());
    }

    public function testPurchase(){
        $this->options = [
            'amount'=>70000.00,
            'items'=>[
                'tithe'=>[
                    'label'=>'Tithes',
                    'value'=>15000.00
                ],
                'seed'=>[
                    'label'=>'Seed Offering',
                    'value'=>25000.00
                ],
                'thanksgiving'=>[
                    'label'=>'Thanksgiving Offering',
                    'value'=>30000.00
                ]
            ],
            'notifyUrl'=>'http://payadmin.celz5.dev/transactions/return',
            Data::GATEWAY_NAME=>Gateway::GATEWAY_WEBPAY,
            Data::TRANSACTION_MEMO=>'offerings',
            Data::CUSTOMER_NAME=>'Anastasia Umoh',
            Data::CUSTOMER_ID=>1,
            Data::TRANSACTION_ID=>$this->transactionId
        ];

        $expectedData = array (
            'gtpay_cust_id' => 1,
            'gtpay_cust_name' => 'Anastasia Umoh',
            'gtpay_mert_id' => '212',
            'gtpay_hash' => 'd917a0bc9c801a831a26a8f2128fd0a359315a2e1340c16af7074984e6578b3597909d8030f19aabf1279005c6302915cdd554ed327683f17b69bb77fda44ee0',
            'gtpay_tranx_id' => '00001513866984',
            'gtpay_tranx_noti_url' => 'http://payadmin.celz5.dev/transactions/return',
            'gtpay_tranx_amt' => 7000000,
            'gtpay_tranx_curr' => '566',
            'gtpay_gway_name' => 'webpay',
        );

        $request = $this->gateway->purchase($this->options)->send();
        $this->assertSame(false,$request->isSuccessful());
        $this->assertSame(true,$request->isRedirect());
        $this->assertEquals(PurchaseResponse::DEMO_REDIRECT_URL,$request->getRedirectUrl());
        $this->assertEquals($expectedData,$request->getRedirectData());

        //d917a0bc9c801a831a26a8f2128fd0a359315a2e1340c16af7074984e6578b3597909d8030f19aabf1279005c6302915cdd554ed327683f17b69bb77fda44ee0
    }


}