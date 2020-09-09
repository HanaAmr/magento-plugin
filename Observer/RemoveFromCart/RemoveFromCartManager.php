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

    public function __construct($product,$customerSession,$clientKeys,$categoryFactory,$productFactory, $gbEnableChecker)
    {
        $this->product = $product;
        $this->customerSession= $customerSession;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory=$categoryFactory;
        $this->productFactory=$productFactory;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    public function execute()
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/RemoveFromCartManager.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $customerId = $this->customerSession->getCustomer()->getId();

        if ($customerId) {
            
            $productId = $this->product->getId();

            $productPrice = $this->product->getPrice();
            $specialPrice = $this->setSpecialPrice();

            $logger->info('category array');
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

                $logger->info('product Id');
                $logger->info($productId);

                $logger->info('customer Id');
                $logger->info($customerId);

                $logger->info('product weight');
                $logger->info($productWeight);

                $logger->info('product cat');
                $logger->info($categoryArray);

                $logger->info('special Price');
                $logger->info($specialPrice);

                $logger->info('product Manufacturer');
                $logger->info($manufacturer);

                $logger->info('gbEnabled');
                $logger->info($gbEnable);

                $logger->info('product Price');
                $logger->info($productPrice);

                $logger->info('api key');
                $logger->info($this->clientKeys->getApiKey());


                if ($gbEnable == "1" && $this->clientKeys->getRemoveFromCart()== 1) {
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

                    $logger->info('Return Code ');
                    $logger->info($res->code);
                    $logger->info($res->body);
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
