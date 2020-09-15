<?php

namespace GbPlugin\Integration\Observer\ViewCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class ViewCart implements ObserverInterface
{

    public function __construct(   
        \GbPlugin\Integration\Observer\ViewCart\ViewCartManager $ViewCartManager
        ){
        $this->ViewCartManager = $ViewCartManager;
    }

    public function execute(Observer $observer)
    {
        $this->ViewCartManager->execute();
    }
}
