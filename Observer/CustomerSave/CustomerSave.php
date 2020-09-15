<?php

namespace GbPlugin\Integration\Observer\CustomerSave;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\CustomerSave\CustomerSaveManager;

class CustomerSave implements ObserverInterface
{

    protected $CustomerSaveManager;

    public function __construct(   
        CustomerSaveManager $CustomerSaveManager
    ){
        $this->CustomerSaveManager = $CustomerSaveManager;
    }

    public function execute(Observer $observer)
    {
        $this->CustomerSaveManager->execute($observer->getData('customer_data_object'));
    }
}
