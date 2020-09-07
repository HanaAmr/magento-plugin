<?php

namespace GbPlugin\Integration\Observer\RemoveFromCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class RemoveFromCart implements ObserverInterface
{
    protected $customerSession;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $GbEnableChecker;

    public function __construct(   
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $GbEnableChecker
    ){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->GbEnableChecker = $GbEnableChecker;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $item = $observer->getQuoteItem();
        $manager = new RemoveFromCartManager($item->getProduct(),
        $this->customerSession,
        $this->clientKeys,
        $this->categoryFactory,
        $this->productFactory,
        $this->GbEnableChecker);
        $manager->execute();
    }
}
