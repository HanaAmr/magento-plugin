<?php

namespace GbPlugin\Integration\Observer\OrderRefund;

require_once BP . '/vendor/autoload.php';

use Exception;
class OrderRefundManager
{

    /**
     * @var creditMemo
     * object of creditMemo
     */
    private $creditMemo;
    protected $clientKeys;
    protected $gbEnableChecker;

    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker
    ){
        $this->clientKeys = $clientKeys;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    public function execute($creditMemo)
    {
        $this->creditMemo = $creditMemo;
        $order = $this->creditMemo->getOrder();
        $orderId = $order->getId();
        $creditMemoId = $this->creditMemo->getIncrementId();


        $customerId = $order->getData('customer_id');

        if ($customerId) {


            $totalQuantityOrdered = $order->getData('total_qty_ordered');


            $totalQuantityRefunded = 0;

            foreach ($order->getAllItems() as $item) {
                $itemQty = $item->getQtyRefunded();
                $totalQuantityRefunded += $itemQty;
            }

            if ($totalQuantityRefunded == $totalQuantityOrdered) {
                try {
                    $gbEnable = $this->gbEnableChecker->check();
                  
    

                    if ($gbEnable === "1") {
                        $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
        
                        $playerUniqueId = $customerId;
                        $transactionId = $creditMemoId; // unique id for this transaction
                        $reversedTransactionId = $orderId; // the id of the transaction to be reversed

                        $res = $gameball->transaction->reverseTransaction($playerUniqueId, $transactionId, $reversedTransactionId);


                    }
                } catch (Exception $e) {
                }

            }

        }
    }

}
