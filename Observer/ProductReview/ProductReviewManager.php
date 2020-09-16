<?php

namespace GbPlugin\Integration\Observer\ProductReview;

use Exception;

require_once BP . '/vendor/autoload.php';

use Magento\Review\Model\Review;

class ProductReviewManager
{
    private $review;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $gbEnableChecker;

    public function __construct(   
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker 
    ){
        $this->clientKeys = $clientKeys;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->gbEnableChecker = $gbEnableChecker;
    }

    
    public function execute($review)
    {
        try {

            $this->review = $review;
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
                    

    
                    if ($gbEnable === "1" && $this->clientKeys->getReview()== 1) {
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

                    }
                }
            }
        } catch (Exception $e) {
        }
    }
}
