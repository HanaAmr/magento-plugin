<?php

namespace GbPlugin\Integration\Observer;

use Exception;
use Magento\Framework\HTTP\Client\Curl;

class InstallTask implements \Magento\Framework\Event\ObserverInterface
{
    private $curl;
    protected $clientKeys;


    public function __construct(Curl $curl, \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys)
    {
        $this->curl = $curl;
        $this->clientKeys = $clientKeys;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/installationTask.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            
            $url = 'https://gb-api.azurewebsites.net/api/v1.0/Bots/Task?code=install_magento_plugin';
            $headers = ["APIKey" => $this->clientKeys->getApiKey()];

            $this->curl->setHeaders($headers);
            $this->curl->post($url, '');

            $result = $this->curl->getBody();
            $bodyAsArray = json_decode($result, true);
            
            $logger->info('Post request sent with body:');
            $logger->info($bodyAsArray);
        } catch (Exception $e) {
            $logger->info($e);
        }
    }
}
