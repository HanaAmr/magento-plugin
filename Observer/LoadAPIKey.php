<?php

namespace GbPlugin\Integration\Observer;


class LoadAPIKey implements \Magento\Framework\Event\ObserverInterface
{
    protected $request;
    protected $_coreSession;


    public function __construct(
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Framework\App\Request\Http $request
    ) {
        $this->request = $request;
        $this->_coreSession = $coreSession;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //***************** Setting ReferralCode ******************

        $params = $this->request->getParams();
        if (array_key_exists('ReferralCode', $params)) {
            $ReferralCode = $params["ReferralCode"];
        } else {
            $ReferralCode = "null";
        }

        if ($ReferralCode != "null") {
            $this->_coreSession->setGameballReferralCode($ReferralCode);
        }    
        
    }
}
