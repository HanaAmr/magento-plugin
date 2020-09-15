<?php

namespace GbPlugin\Integration\Observer\OrderRefund;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use GbPlugin\Integration\Observer\OrderRefund\OrderRefundManager;

class OrderRefund implements ObserverInterface
{
     
    public function __construct(
        OrderRefundManager $OrderRefundManager
    ) {
        $this->OrderRefundManager = $OrderRefundManager;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */

    public function execute(Observer $observer)
    {
        $creditMemo = $observer->getEvent()->getCreditmemo();
        $this->OrderRefundManager->execute($creditMemo);
       
    }

}
