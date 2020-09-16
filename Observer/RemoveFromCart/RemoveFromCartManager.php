<?php

namespace GbPlugin\Integration\Observer\RemoveFromCart;

require_once BP . '/vendor/autoload.php';

use Exception;

class RemoveFromCartManager
{
    private $product;
    protected $customerSession;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $gbEnableChecker;

    public function __construct(   
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker
    ){
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    public function execute($product)
    {


        $this->product = $product;
        $customerId = $this->customerSession->getCustomer()->getId();

        if ($customerId) {
            
            $productId = $this->product->getId();

            $productPrice = $this->product->getPrice();
            $specialPrice = $this->setSpecialPrice();

            $productData = $this->productFactory->create()->load($productId);
            $categoryIds = $productData->getData('category_ids');
            $manufacturer=$productData->getAttributeText('manufacturer');

                     /**
                     * @var type{Array} categoryArray
                     * Array of all categories that are inside an order
                     *
                     */
                    $categoryArray = array();

                    if ($categoryIds) {
                        foreach ($categoryIds as $categoryId) {
                            $category =$this->categoryFactory->create()->load($categoryId);
                            $categoryName = $category->getName();
                            array_push($categoryArray, $categoryName);
                        }
                    }

            $productWeight = $this->product->getData('weight');
            

            try
            {
                $gbEnable = $this->gbEnableChecker->check();

                if ($gbEnable === "1" && $this->clientKeys->getRemoveFromCart()== 1) {
                  $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
  
                    $playerRequest = new \Gameball\Models\PlayerRequest();
                    $playerRequest->playerUniqueId = (string) $customerId;

                    $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                    $eventRequest->addEvent('remove_from_cart');
                    if ($productPrice) {
                        $eventRequest->addMetaData('remove_from_cart', 'price', $productPrice);
                    }
                    if ($productWeight) {
                        $eventRequest->addMetaData('remove_from_cart', 'weight', +$productWeight);
                    }
                    if ($categoryArray) {$eventRequest->addMetaData('remove_from_cart', 'category', $categoryArray);
                    }
                    if ($specialPrice) {
                        $eventRequest->addMetaData('remove_from_cart', 'special_price', $specialPrice);
                    }
                    if ($manufacturer) {
                      $eventRequest->addMetaData('remove_from_cart', 'manufacturer', $manufacturer);
                    }

                    $res = $gameball->event->sendEvent($eventRequest);

                }
            } catch (Exception $e) {
            }

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
