<?php

namespace GbPlugin\Integration\Observer\ViewCart;
use Exception;

require_once BP . '/app/code/GbPlugin/vendor/autoload.php';


class ViewCartManager
{
    
    private $customerSession;
    protected $clientKeys;
    protected $GbEnableChecker;
    protected $cart;

    public function __construct($customerSession,$clientKeys,$GbEnableChecker,$cart
    ){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->GbEnableChecker= $GbEnableChecker; 
        $this->cart= $cart; 
    }


    public function execute()
    {
        try{
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cartViewed1.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Cart Viewed !');
        $logger->info('all cart  items details');
        $customerId = $this->customerSession->getCustomer()->getId();

        if ($customerId) {
        $items = $this->cart->getQuote()->getAllItems();
        $logger->info('prod quantity');
        $productCount = count($items);
        $logger->info($productCount);
        $totalItems = 0;

        foreach ($items as $item) {
            $qty = $item->getQty();
            $totalItems += $qty;
        }
        $logger->info('total count');
        $logger->info($totalItems);

        
            $logger->info('customer Id');
            $logger->info($customerId);

            $gbEnable = $this->GbEnableChecker->check();

            $logger->info('gbEnabled');
            $logger->info($gbEnable);

            $logger->info('api key');
            $logger->info($this->clientKeys->getApiKey());

            
            if ($gbEnable == "1" && $this->clientKeys->getViewCart()== 1) {
                $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());

                $playerRequest = new \Gameball\Models\PlayerRequest();

                $playerRequest->playerUniqueId = (string) $customerId;
                $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                $eventRequest->addEvent('view_cart');

                if ($totalItems) {$eventRequest->addMetaData('view_cart', 'total', $totalItems);}
                if ($productCount) {$eventRequest->addMetaData('view_cart', 'products_count', $productCount);}

                $res = $gameball->event->sendEvent($eventRequest);

                $logger->info('Return Code ');

                $logger->info($res->code);
                $logger->info($res->body);
            }

        }
    }
    catch(Exception $e){}
    }
}

