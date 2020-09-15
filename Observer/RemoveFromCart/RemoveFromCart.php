<?php

namespace GbPlugin\Integration\Observer\RemoveFromCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class RemoveFromCart implements ObserverInterface
{
    protected $customerSession;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $GbEnableChecker;

    
    public function __construct(   
        \GbPlugin\Integration\Observer\RemoveFromCart\RemoveFromCartManager $RemoveFromCartManager
        ){
        $this->RemoveFromCartManager = $RemoveFromCartManager;
    }
   /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $this->RemoveFromCartManager->execute($item->getProduct());
    }

}
