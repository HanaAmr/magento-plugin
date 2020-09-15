<?php

namespace GbPlugin\Integration\Observer\ViewProduct;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\ViewProduct\ViewProductManager;


class ViewProduct implements ObserverInterface
{
    protected $clientKeys;
    protected $customerSession;
    protected $categoryFactory;
    protected $gbEnableChecker;
    protected $ViewProductManager;


    public function __construct(   
        \GbPlugin\Integration\Observer\ViewProduct\ViewProductManager $ViewProductManager){
        $this->ViewProductManager = $ViewProductManager;
    }

    public function execute(Observer $observer)
    {
        $this->ViewProductManager->execute($observer->getProduct());

    }
}
