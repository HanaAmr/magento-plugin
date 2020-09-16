<?php

namespace GbPlugin\Integration\Observer\ViewProduct;

require_once BP . '/vendor/autoload.php';

use Exception;

class ViewProductManager
{
    private $product;

    protected $clientKeys;
    private $customerSession;
    private $categoryFactory;
    protected $gbEnableChecker;

    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker) 
    {
        $this->customerSession = $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->gbEnableChecker = $gbEnableChecker;
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
                     *
                     */
                    $categoryArray = array();

                    if ($categoryIds) {
                        foreach ($categoryIds as $categoryId) {
                            $category=$this->categoryFactory->create()->load($categoryId);
                            $categoryName = $category->getName();
                            array_push($categoryArray, $categoryName);
                        }
                    }

                $productName = $this->product->getName();
                $productPrice = $this->product->getPrice();
                $productWeight = $this->product->getData('weight');
                $stockData = $this->product->getData('quantity_and_stock_status');
                $productStock = $stockData['qty'];
                $specialPrice = $this->setSpecialPrice();
                $manufacturer=$this->product->getAttributeText('manufacturer');
                $gbEnable = $this->gbEnableChecker->check();


                if ($gbEnable === "1" && $this->clientKeys->getViewProduct()== 1) {
                    $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());

                    $playerRequest = new \Gameball\Models\PlayerRequest();

                    $playerRequest->playerUniqueId = (string) $customerId;

                    $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                    $eventRequest->addEvent('view_product');
                    if ($productId) {$eventRequest->addMetaData('view_product', 'id', $productId);}

                    if ($productName) {$eventRequest->addMetaData('view_product', 'name', $productName);}

                    if ($productPrice) {$eventRequest->addMetaData('view_product', 'price', $productPrice);}

                    if ($productWeight) {$eventRequest->addMetaData('view_product', 'weight', +$productWeight);}

                    if ($categoryArray) {$eventRequest->addMetaData('view_product', 'category', $categoryArray);}

                    if ($productStock) {$eventRequest->addMetaData('view_product', 'stock', $productStock);}

                    if ($specialPrice) {$eventRequest->addMetaData('view_product', 'special_price', $specialPrice);}

                    if ($manufacturer) {$eventRequest->addMetaData('view_product', 'manufacturer', $manufacturer);}

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
