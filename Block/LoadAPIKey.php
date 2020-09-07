<?php
namespace GbPlugin\Integration\Block;

class LoadAPIKey extends \Magento\Framework\View\Element\Template
{
    protected $clientKeys;
    protected $customerSession;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys
    ) {
        parent::__construct($context);
        $this->clientKeys = $clientKeys;
        $this->customerSession = $customerSession;
    }

    public function getAPIKey()
    {
        return $this->clientKeys->getApiKey();
    }

    public function getCustomerSession()
    {
        return $this->customerSession;
    }

}
