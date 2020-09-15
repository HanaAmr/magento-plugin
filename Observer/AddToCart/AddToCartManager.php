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
            
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/AddToCartt.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

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

                $logger->info('product Id');
                $logger->info($productId);

                $logger->info('customer Id');
                $logger->info($customerId);

                $logger->info('product Price');
                $logger->info($productPrice);

                $logger->info('product weight');
                $logger->info($productWeight);

                $logger->info('product cat');
                $logger->info($categoryArray);

                $logger->info('product manufacturer');
                $logger->info($manufacturer);

                $logger->info('special Price');
                $logger->info($specialPrice);

                $logger->info('gbEnabled');
                $logger->info($gbEnable);

                $logger->info('api key');
                $logger->info($this->clientKeys->getApiKey());

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

                    $logger->info('Return Code ');
                    $logger->info($res->code);
                    $logger->info($res->body);
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
