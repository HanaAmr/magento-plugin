<?php

namespace GbPlugin\Integration\Observer\RegisterSuccess;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Session\SessionManagerInterface;

class RegisterSuccess implements ObserverInterface
{
    private $customerRepositoryInterface;
    private $coreSession;
    private $request;
    protected $clientKeys;
    protected $customerModel;

    public function __construct(CustomerRepositoryInterface $customerRepositoryInterface, SessionManagerInterface $coreSession, Http $request,\GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
    \Magento\Customer\Model\Customer $customerModel)
    {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->coreSession = $coreSession;
        $this->request = $request;
        $this->clientKeys = $clientKeys;
        $this->customerModel = $customerModel;
    }

    public function execute(Observer $observer)
    {
        $manager = new RegisterSuccessManager($observer->getEvent()->getData('customer'), $this->customerRepositoryInterface, $this->coreSession, $this->request,$this->clientKeys,$this->customerModel);
        $manager->execute();
    }
}
