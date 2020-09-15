<?php

namespace GbPlugin\Integration\Observer\Shared;

use Magento\Framework\HTTP\Client\Curl;

class GbEnableChecker
{
    protected $curl;
    protected $clientKeys;

    public function __construct(Curl $curl, \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys)
    {
        $this->curl = $curl;
        $this->clientKeys = $clientKeys;
    }
    public function check()
    {
        $url = 'https://api.gameball.co/api/v1.0/Bots/BotSettings';
        $headers = ["APIKey" => $this->clientKeys->getApiKey()];

        $this->curl->setHeaders($headers);
        $this->curl->get($url);

        $result = $this->curl->getBody();
        $bodyAsArray = json_decode($result, true);
        $gbEnabledFlag = $bodyAsArray['response']['gbEnabled'];

        return (string) $gbEnabledFlag;
    }
}
