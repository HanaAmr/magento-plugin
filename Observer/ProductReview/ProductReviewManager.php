<?php

namespace GbPlugin\Integration\Observer\ProductReview;

use Exception;

require_once BP . '/app/code/GbPlugin/vendor/autoload.php';

use Magento\Review\Model\Review;

class ProductReviewManager
{
    private $review;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $gbEnableChecker;

    public function __construct($review,$clientKeys,$categoryFactory,$productFactory, $gbEnableChecker)
    {
        $this->review = $review;
        $this->clientKeys = $clientKeys;
        $this->categoryFactory=$categoryFactory;
        $this->productFactory=$productFactory;
        $this->gbEnableChecker = $gbEnableChecker;
       
    }

    public function execute()
    {
        try {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ReviewProduct1.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            
            $customerId = $this->review->getData('customer_id');

            if ($customerId) {
                $status = $this->review->getData('status_id');

                if ($status == Review::STATUS_APPROVED) {

                    $productId = $this->review->getData('entity_pk_value');

                    $product = $this->productFactory->create()->load($productId);

                    $productId = $product->getId();

                    $categoryIds = $product->getData('category_ids');
                     /**
                     * @var type{Array} categoryArray
                     * Array of all categories that are inside an order
                     *
                     */
                    $categoryArray = array();

                    if ($categoryIds) {
                        foreach ($categoryIds as $categoryId) {
                            $category = $this->categoryFactory->create()->load($categoryId);
                            $categoryName = $category->getName();
                            array_push($categoryArray, $categoryName);
                        }
                    }

                    $productWeight = $product->getData('weight');
                    $manufacturer=$product->getAttributeText('manufacturer');
                    $gbEnable = $this->gbEnableChecker->check();
                    
                    $logger->info('prod id');
                    $logger->info($productId);

                    $logger->info('product Id');
                    $logger->info($productId);

                    $logger->info('customer Id');
                    $logger->info($customerId);

                    $logger->info('product weight');
                    $logger->info($productWeight);

                    $logger->info('manufacturer');
                    $logger->info($manufacturer);

                    $logger->info('product cat');
                    $logger->info($categoryArray);

                    $logger->info('gbEnabled');
                    $logger->info($gbEnable);

                    $logger->info('api key');
                    $logger->info($this->clientKeys->getApiKey());
    

                    if ($gbEnable == "1" && $this->clientKeys->getReview()== 1) {
                      $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
      
                        $playerRequest = new \Gameball\Models\PlayerRequest();

                        $playerRequest->playerUniqueId = (string) $customerId;

                        $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                        $eventRequest->addEvent('review');
                        if ($productId) {
                            $eventRequest->addMetaData('review', 'id', $productId);
                        }
                        if ($productWeight) {
                            $eventRequest->addMetaData('review', 'weight', +$productWeight);
                        }
                        if ($categoryArray) {
                            $eventRequest->addMetaData('review', 'category', $categoryArray);
                        }
                        if ($manufacturer) {
                          $eventRequest->addMetaData('review', 'manufacturer', $manufacturer);
                      }

                        $res = $gameball->event->sendEvent($eventRequest);

                        $logger->info('Return Code ');
                        $logger->info($res->code);
                    }
                }
            }
        } catch (Exception $e) {
        }
    }
}