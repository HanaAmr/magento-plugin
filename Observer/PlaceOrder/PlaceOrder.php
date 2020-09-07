<?php

namespace GbPlugin\Integration\Observer\PlaceOrder;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class PlaceOrder implements ObserverInterface
{
    protected $httpClientFactory;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $gbEnableChecker;

    public function __construct(\Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
    \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
    \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
    \Magento\Catalog\Model\ProductFactory $productFactory,
    \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker 
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory= $categoryFactory;
        $this->productFactory= $productFactory;
        $this->gbEnableChecker = $gbEnableChecker;

    }
    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $manager = new PlaceOrderManager($order,
        $this->httpClientFactory,
        $this->clientKeys,
        $this->categoryFactory,
        $this->productFactory,
        $this->gbEnableChecker);
        $manager->execute();

    }
}
