<?php
namespace DavidUmoh\GtPay\Tests;

use DavidUmoh\GtPay\Gateway;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase{

    public $gateway;

    public $options;

    const HASH_KEY = 'D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F';
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setMerchantId('212');
        $this->gateway->setHashKey(self::HASH_KEY);
        $this->gateway->setGatewayFirst('no');
        $this->gateway->setGatewayName(Gateway::GATEWAY_BANK);
    }

    public function testGateway(){

        $this->assertEquals('212',$this->gateway->getMerchantId());
        $this->assertEquals(self::HASH_KEY,$this->gateway->getHashKey());
        $this->assertEquals(Gateway::GATEWAY_BANK,$this->gateway->getGatewayName());
        $this->assertEquals('no',$this->gateway->getGatewayFirst());
    }


}