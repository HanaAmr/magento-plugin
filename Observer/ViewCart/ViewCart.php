<?php

namespace GbPlugin\Integration\Observer\ViewCart;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;


class ViewCart implements ObserverInterface
{
    protected $customerSession;
    protected $clientKeys;
    protected $GbEnableChecker;
    protected $cart;

    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $GbEnableChecker,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart


    ){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->GbEnableChecker = $GbEnableChecker;
        $this->cart = $cart;
    }

    public function execute(Observer $observer)
    {
        $manager = new ViewCartManager($this->customerSession,$this->clientKeys,$this->GbEnableChecker,$this->cart);
        $manager->execute();

    }
}
