<?php

namespace GbPlugin\Integration\Observer\Shared;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class ClientKeysTable extends AbstractHelper
{

    const XML_PATH_FIELD = 'gameball_keys/gameball/';
    protected $scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {

        $this->scopeConfig = $scopeConfig;

    }

    public function getApiKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'api_key', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getTransactionKey()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'transaction_key', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getViewProduct()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'view_product', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getAddToCart()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'add_to_cart', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getViewCart()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'view_cart', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getReview()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'review', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getPlaceOrder()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'place_order', ScopeInterface::SCOPE_STORE, null
        );
    }
    public function getRemoveFromCart()
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_FIELD . 'remove_from_cart', ScopeInterface::SCOPE_STORE, null
        );
    }

}
