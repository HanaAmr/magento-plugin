<?php

namespace GbPlugin\Integration\Observer\CustomerLogin;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\CustomerLogin\CustomerLoginManager;


class CustomerLogin implements ObserverInterface
{

    protected $CustomerLoginManager;

    public function __construct(   
        CustomerLoginManager $CustomerLoginManager
    ){
        $this->CustomerLoginManager = $CustomerLoginManager;
    }

    public function execute(Observer $observer)
    {
        $this->CustomerLoginManager->execute($observer->getEvent()->getCustomer());
    }
}
