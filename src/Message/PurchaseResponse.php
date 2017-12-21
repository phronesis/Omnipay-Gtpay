<?php

namespace DavidUmoh\GtPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface{

    const DEMO_REDIRECT_URL = "https://gtweb2.gtbank.com/GTPay/tranx.aspx";

    const LIVE_REDIRECT_URL = "https://ibank.gtbank.com/GTPay/Tranx.aspx";

    public function getRedirectUrl()
    {
        return ($this->request->getParameters()['testMode'])?self::DEMO_REDIRECT_URL:self::LIVE_REDIRECT_URL;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function isSuccessful()
    {
        return false;
    }

    public function getRedirectData()
    {
       return $this->data;
    }

    public function isRedirect()
    {
        return true;
    }


}