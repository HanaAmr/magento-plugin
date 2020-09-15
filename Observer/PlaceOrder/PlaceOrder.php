<?php

namespace GbPlugin\Integration\Observer\PlaceOrder;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\PlaceOrder\PlaceOrderManager;


class PlaceOrder implements ObserverInterface
{
    
    public function __construct(
        PlaceOrderManager $PlaceOrderManager
    ) {
        $this->PlaceOrderManager = $PlaceOrderManager;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $this->PlaceOrderManager->execute($order);
    }
}
