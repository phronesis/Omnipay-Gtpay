<?php
namespace Omnipay\Gtpay\Message;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequest extends AbstractRequest
{

    public function getData()
    {
        $data = $this->httpRequest->request->all();

        if (empty($data)) {
            throw new InvalidRequestException('No Request Body Found');
        }

        return $this->mergeResponse($this->queryGateway(), $data);
    }

    public function sendData($data)
    {
        return new CompletePurchaseResponse($this, $data);
    }


    public function queryGateway()
    {
        $param = [
            'tranxid' => $this->getTransactionId(),
            'amount' => $this->getAmountInteger(),
            'mertid' => $this->getMerchantId(),
            'hash'=>$this->getVerificationHash()
        ];
        $response = $this->httpClient->get(
            $this->getWebserviceUrl(),
            null,
            ['query'=>$param,'read_timeout'=>60]
        )->send();

        if ($response->getStatusCode() !== 200) {
            throw new InvalidResponseException();
        }
        $body = (string) $response->getBody();
        return json_decode($body, true);
    }

    private function getVerificationHash()
    {
        $hashString = $this->getMerchantId() . $this->getTransactionId() . $this->getHashKey();
        return hash('sha512', $hashString);
    }

    private function mergeResponse($newResponse, $data)
    {
        if (empty($data)) {
            return $newResponse;
        }
        return array_merge($data, $newResponse);
    }
}
