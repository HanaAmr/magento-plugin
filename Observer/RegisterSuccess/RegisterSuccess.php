<?php

namespace GbPlugin\Integration\Observer\RegisterSuccess;

use GbPlugin\Integration\Observer\RegisterSuccess\RegisterSuccessManager;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RegisterSuccess implements ObserverInterface
{
    protected $RegisterSuccessManager;


    public function __construct(RegisterSuccessManager $RegisterSuccessManager)
    {
        $this->RegisterSuccessManager = $RegisterSuccessManager;
    }

    public function execute(Observer $observer)
    {
        $this->RegisterSuccessManager->execute($observer->getEvent()->getData('customer'));
    }
}
