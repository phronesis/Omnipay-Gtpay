<?php
namespace Omnipay\Gtpay\Tests;

use Omnipay\Gtpay\Gateway;
use Omnipay\Gtpay\Message\Data;
use Omnipay\Gtpay\Message\PurchaseResponse;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase{

    public $gateway;

    public $options;

    const HASH_KEY = 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F';
    private $transactionId = '00001513876649';
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('17');
        $this->gateway->setHashKey(self::HASH_KEY);
        $this->gateway->setGatewayFirst('no');
        $this->gateway->setGatewayName(Gateway::GATEWAY_BANK);
        $this->gateway->setCurrency('NGN');
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
            'notifyUrl'=>'http://payadmin.celz5.dev/transactions/notify',
            Data::GATEWAY_NAME=>Gateway::GATEWAY_WEBPAY,
            Data::TRANSACTION_MEMO=>'offerings',
            Data::CUSTOMER_NAME=>'Anastasia Umoh',
            Data::CUSTOMER_ID=>1,
            Data::TRANSACTION_ID=>$this->transactionId
        ];
    }

    public function testGateway(){

        $this->assertEquals('17',$this->gateway->getMerchantId());
        $this->assertEquals(self::HASH_KEY,$this->gateway->getHashKey());
        $this->assertEquals(Gateway::GATEWAY_BANK,$this->gateway->getGatewayName());
        $this->assertEquals('no',$this->gateway->getGatewayFirst());
    }

    public function testPurchase(){


        $expectedData = array (
            'gtpay_cust_id' => 1,
            'gtpay_cust_name' => 'Anastasia Umoh',
            'gtpay_mert_id' => '17',
            'gtpay_hash' => 'ca16c4336135711dff0ada1530dbc058f5805c6fd595b3e18d557e08fd532f95a0795e1bef998c4d92566c13b445c8623adc3069614e8f2abbf60e7ea5c575f2',
            'gtpay_tranx_id' => '00001513876649',
            'gtpay_tranx_noti_url' => 'http://payadmin.celz5.dev/transactions/notify',
            'gtpay_tranx_amt' => 7000000,
            'gtpay_tranx_curr' => '566',
            'gtpay_gway_name' => 'webpay',
        );

        $request = $this->gateway->purchase($this->options)->send();
        $this->assertSame(false,$request->isSuccessful());
        $this->assertSame(true,$request->isRedirect());
        $this->assertEquals(PurchaseResponse::DEMO_REDIRECT_URL,$request->getRedirectUrl());
        $this->assertEquals($expectedData,$request->getRedirectData());

        }

    public function testCompletePurchase(){
        $this->getHttpRequest()->initialize([],array (
            'gtpay_tranx_id' => '00001513876649',
            'gtpay_tranx_status_code' => '00',
            'gtpay_tranx_curr' => 'NGN',
            'gtpay_tranx_status_msg' => 'Approved by Financial Institution',
            'gtpay_tranx_amt' => '70000.00',
            'gtpay_cust_id' => '1',
            'gtpay_echo_data' => 'Anastasia Umoh',
            'site_redirect_url' => 'http://payadmin.celz5.dev/transactions/notify',
            'gtpay_gway_name' => 'webpay',
            'gtpay_tranx_hash' => '30F0070926A0CAEDF77493DAD4E201F4C18D02317E3B3402CC6FE7900282B51557AA5027C6B32BC958823CD66D21AEAB2C59884D525A642B672EF91EB4D047B4',
            'gtpay_verification_hash' => '69033F0BE25FAE206EE9C55D1E477226564CA3151B82CF3BCA1E8E07F9FBDCBFC423FF1952FB20842087AA992F243918B1305DF6F7768A96EFEA86BF63DAE202',
            'gtpay_full_verification_hash' => '0ABBEE8521279BBB9E0748F4302CF9C7DD78419DD0925B64A1DE669B082117ACFE7631BEB7A5F5A387CB99B9269038C5379F365BC6F9C1166DFA9E9C7D882B88',
            'gtpay_tranx_amt_small_denom' => '7000000',
        ));
        //@TODO mock http client so that it does not do actual network requests to verify gateway status
        $response = $this->gateway->completePurchase($this->options)->send();
        $this->assertSame(true,$response->isSuccessful());
        $this->assertEquals('FBN|WEB|WDM|21-12-2017|330132',$response->getTransactionReference());
        $this->assertEquals('00001513876649',$response->getTransactionId());

    }

    public function testWrongAmountPaid(){
        $this->markTestIncomplete('To be done!');
    }

    public function testSpoofedRequest(){
        $this->markTestIncomplete('To be done');
    }

    public function testEmptyResponse(){
        $this->markTestIncomplete('To be done!');
    }


}