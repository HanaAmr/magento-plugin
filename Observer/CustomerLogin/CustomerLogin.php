<?php

namespace GbPlugin\Integration\Observer\CustomerLogin;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    protected $clientKeys;
    protected $customerModel;
     
    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Customer\Model\Customer $customerModel
    ){
        $this->clientKeys = $clientKeys;
        $this->customerModel = $customerModel;
    }

    public function execute(Observer $observer)
    {
        $manager = new CustomerLoginManager($observer->getEvent()->getCustomer(),$this->clientKeys,$this->customerModel);
        $manager->execute();
    }
}
