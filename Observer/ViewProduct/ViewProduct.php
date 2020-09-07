<?php

namespace GbPlugin\Integration\Observer\ViewProduct;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ViewProduct implements ObserverInterface
{
    protected $clientKeys;
    protected $customerSession;
    protected $categoryFactory;
    protected $gbEnableChecker;

    public function __construct(   
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    public function execute(Observer $observer)
    {

        $manager = new ViewProductManager($observer->getProduct(),
        $this->customerSession,
        $this->clientKeys,
        $this->categoryFactory,
        $this->gbEnableChecker);
        $manager->execute();
    }
}
