<?php

namespace GbPlugin\Integration\Observer\AddToCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\AddToCart\AddToCartManager;

class AddToCart implements ObserverInterface
{
    protected $AddToCartManager;

    public function __construct(
        AddToCartManager $AddToCartManager
    ) {
        $this->AddToCartManager = $AddToCartManager;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $this->AddToCartManager->execute($observer->getProduct());
    }
}
