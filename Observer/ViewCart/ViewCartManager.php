<?php

namespace GbPlugin\Integration\Observer\ViewCart;
use GbPlugin\Integration\Observer\Product\ViewProductManager;

use Exception;

require_once BP . '/vendor/autoload.php';


class ViewCartManager
{
    
    private $customerSession;
    protected $clientKeys;
    protected $GbEnableChecker;
    protected $cart;

    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $GbEnableChecker,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart
    ){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->GbEnableChecker = $GbEnableChecker;
        $this->cart = $cart;
    }


    public function execute()
    {
        try{

        $customerId = $this->customerSession->getCustomer()->getId();

        if ($customerId) {
        $items = $this->cart->getQuote()->getAllItems();
        $productCount = count($items);
        $totalItems = 0;

        foreach ($items as $item) {
            $qty = $item->getQty();
            $totalItems += $qty;
        }


            $gbEnable = $this->GbEnableChecker->check();

            
            if ($gbEnable === "1" && $this->clientKeys->getViewCart()== 1) {
                $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());

                $playerRequest = new \Gameball\Models\PlayerRequest();

                $playerRequest->playerUniqueId = (string) $customerId;
                $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                $eventRequest->addEvent('view_cart');

                if ($totalItems) {$eventRequest->addMetaData('view_cart', 'total', $totalItems);}
                if ($productCount) {$eventRequest->addMetaData('view_cart', 'products_count', $productCount);}

                $res = $gameball->event->sendEvent($eventRequest);

            }

        }
    }
    catch(Exception $e){}
    }
}

