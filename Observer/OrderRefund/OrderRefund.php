<?php

namespace GbPlugin\Integration\Observer\OrderRefund;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class OrderRefund implements ObserverInterface
{
    protected $clientKeys;
    protected $gbEnableChecker;

    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker
    ){
        $this->clientKeys = $clientKeys;
        $this->gbEnableChecker = $gbEnableChecker;
    }
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */

    public function execute(Observer $observer)
    {
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $manager = new OrderRefundManager($creditmemo,$this->clientKeys, $this->gbEnableChecker);
        $manager->execute();
    }

}
