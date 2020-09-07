<?php

namespace GbPlugin\Integration\Observer\CustomerSave;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerSave implements ObserverInterface
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
        $manager = new CustomerSaveManager($observer->getData('customer_data_object'),$this->clientKeys,$this->customerModel);
        $manager->execute();
    }
}
