<?php

namespace GbPlugin\Integration\Observer\AddToCart;

require_once BP . '/vendor/autoload.php';

use Exception;

class AddToCartManager
{
    private $product;

    private $customerSession;
    protected $clientKeys;
    protected $categoryFactory;
    protected $GbEnableChecker;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $GbEnableChecker) 
    {
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->GbEnableChecker = $GbEnableChecker;
    }

    public function execute($product)
    {
        try {
                 

            $this->product = $product;
            $customerId = $this->customerSession->getCustomer()->getId();

            if ($customerId) {
                $productId = $this->product->getId();
                $categoryIds = $this->product->getData('category_ids');
                /**
                 * @var type{Array} categoryArray
                 * Array of all categories that are inside an order
                 */
                $categoryArray = array();

                if ($categoryIds) {
                    foreach ($categoryIds as $categoryId) {
                        $category = $this->categoryFactory->create()->load($categoryId);
                        $categoryName = $category->getName();
                        array_push($categoryArray, $categoryName);
                    }
                }

                $productName = $this->product->getName();
                $productPrice = $this->product->getPrice();
                $manufacturer = $this->product->getAttributeText('manufacturer');
                $specialPrice = $this->setSpecialPrice();
                $productWeight = $this->product->getData('weight');
                $gbEnable = $this->GbEnableChecker->check();



                if ($gbEnable == "1" && $this->clientKeys->getAddToCart() == 1) {
                    $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());

                    $playerRequest = new \Gameball\Models\PlayerRequest();

                    $playerRequest->playerUniqueId = (string) $customerId;

                    $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                    $eventRequest->addEvent('add_to_cart');

                    if ($productId) {
                        $eventRequest->addMetaData('add_to_cart', 'id', $productId);
                    }
                    if ($productPrice) {
                        $eventRequest->addMetaData('add_to_cart', 'price', $productPrice);
                    }
                    if ($productWeight) {
                        $eventRequest->addMetaData('add_to_cart', 'weight', +$productWeight);
                    }
                    if ($categoryArray) {
                        $eventRequest->addMetaData('add_to_cart', 'category', $categoryArray);
                    }
                    if ($specialPrice) {
                        $eventRequest->addMetaData('add_to_cart', 'special_price', $specialPrice);
                    }
                    if ($manufacturer) {
                        $eventRequest->addMetaData('add_to_cart', 'manufacturer', $manufacturer);
                    }

                    $res = $gameball->event->sendEvent($eventRequest);

                   
                }
            }
        } catch (Exception $e) {
        }
    }
    /**
     * @method setSpecialPrice
     * @return void
     *  Method made to check if the product has a valid special Price data
     */
    private function setSpecialPrice()
    {
        $specialEndDate = $this->product->getData('special_to_date');
        $specialEndDateFormatted = date('Y-m-d', strtotime($specialEndDate));
        $specialBeginDate = $this->product->getData('special_from_date');
        $specialBeginDateFormatted = date('Y-m-d', strtotime($specialBeginDate));
        $dateNow = date('Y-m-d');
        if ($specialEndDateFormatted >= $dateNow && $dateNow >= $specialBeginDateFormatted) {
            return $this->product->getData('special_price');
        }
    }
}
