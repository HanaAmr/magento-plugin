<?php

namespace GbPlugin\Integration\Observer\OrderRefund;

require_once BP . '/app/code/GbPlugin/vendor/autoload.php';

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
    
    public function __construct($creditmemo, $clientKeys, $gbEnableChecker)
    {
        $this->creditMemo = $creditmemo;
        $this->clientKeys = $clientKeys;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    public function execute()
    {

        $order = $this->creditMemo->getOrder();
        $orderId = $order->getId();
        $creditMemoId = $this->creditMemo->getIncrementId();
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/RefundManager.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $customerId = $order->getData('customer_id');

        if ($customerId) {
            $logger->info('customer refunded something!');
            $logger->info($creditMemoId);
            $logger->info('order id !');
            $logger->info($orderId);

            $logger->info('order Data total ordered !');
            $totalQuantityOrdered = $order->getData('total_qty_ordered');
            $logger->info($totalQuantityOrdered);

            $logger->info('order Data total refunded !');
            $logger->info($order->getData('total_refunded'));

            $logger->info('order sub total  !');
            $logger->info($order->getSubtotal());

            $logger->info('order sub total refunded !');
            $logger->info($order->getData('subtotal_refunded'));

            $totalQuantityRefunded = 0;

            foreach ($order->getAllItems() as $item) {
                $logger->info($item->getQtyOrdered());
                $logger->info($item->getSku());
                $logger->info('item qty refunded');
                $itemQty = $item->getQtyRefunded();
                $logger->info($itemQty);
                $totalQuantityRefunded += $itemQty;
            }

            if ($totalQuantityRefunded == $totalQuantityOrdered) {
                $logger->info('Order Deleted');
                try {
                    $gbEnable = $this->gbEnableChecker->check();
                   
                    $logger->info('gbEnabled');
                    $logger->info($gbEnable);

                    $logger->info('api key');
                    $logger->info($this->clientKeys->getApiKey());
    

                    if ($gbEnable == "1") {
                        $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
        
                        $playerUniqueId = $customerId;
                        $transactionId = $creditMemoId; // unique id for this transaction
                        $reversedTransactionId = $orderId; // the id of the transaction to be reversed

                        $res = $gameball->transaction->reverseTransaction($playerUniqueId, $transactionId, $reversedTransactionId);
                        $logger->info('Return Code');
                        $logger->info($res->body);
                        $logger->info($res->code);

                    }
                } catch (Exception $e) {
                    $logger->info($e);
                }

            }

        }
    }

}
