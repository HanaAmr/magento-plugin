<?php

namespace GbPlugin\Integration\Observer\PlaceOrder;

require_once BP . '/vendor/autoload.php';

use Exception;

class PlaceOrderManager
{
    /**
     * @var order
     * object of class order
     */
    private $order;

    private $httpClientFactory;
    protected $clientKeys;
    protected $categoryFactory;
    protected $productFactory;
    protected $gbEnableChecker;

    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \GbPlugin\Integration\Observer\Shared\ClientkeysTable $clientKeys,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory, 
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \GbPlugin\Integration\Observer\Shared\GbEnableChecker $gbEnableChecker 
        )
        {
            $this->httpClientFactory = $httpClientFactory;
            $this->clientKeys = $clientKeys;
            $this->categoryFactory= $categoryFactory;
            $this->productFactory= $productFactory;
            $this->gbEnableChecker = $gbEnableChecker;
    
        }

    public function execute($order)
    {

        $this->order = $order;

        try {
             if ($this->order->getState() === 'complete' && $this->order->getOrigData('state') != 'complete') {
                {   
                $customerId = $this->order->getData('customer_id');

                if ($customerId) {
                    $orderId = $this->order->getId();
                    $amount = $this->order->getGrandTotal();
                    $discounted = $this->setIsDiscounted();
                    $weight = $this->order->getData('weight');

                    /**
                     * @var type{Array} categoryArray
                     * Array of all categories that are inside an order
                     *
                     */
                    $categoryArray = array();
                    /**
                     * @var type{Array} manufacturersArray
                     * Array of all product manufacturers that are inside an order
                     *
                     */
                    $manufacturersArray = array();

                    foreach ($this->order->getAllItems() as $item) {
                        $productId = $item->getProductId();
                        // Get a product using product Id through object manager
                        $product = $this->productFactory->create()->load($productId);
                        $manufacturer = $product->getAttributeText('manufacturer');
                        if ($manufacturer) {array_push($manufacturersArray, $manufacturer);}
                        $categoryIds = $product->getData('category_ids');
                        if ($categoryIds) {
                            foreach ($categoryIds as $categoryId) {
                                $category = $this->categoryFactory->create()->load($categoryId);
                                $categoryName = $category->getName();
                                array_push($categoryArray, $categoryName);
                            }
                        }
                    }
                    
                    $gbEnable = $this->gbEnableChecker->check();


                    if ($gbEnable === "1" && $this->clientKeys->getPlaceOrder()== 1) {
                        $gameball = new \Gameball\GameballClient($this->clientKeys->getApiKey(), $this->clientKeys->getTransactionKey());
        
                        $playerRequest = new \Gameball\Models\PlayerRequest();

                        $playerRequest->playerUniqueId = (string) $customerId;

                        $eventRequest = \Gameball\Models\EventRequest::factory($playerRequest);

                        $eventRequest->addEvent('place_order');

                        if ($amount) {$eventRequest->addMetaData('place_order', 'amount', $amount);}
                        if ($categoryArray) {$eventRequest->addMetaData('place_order', 'category', $categoryArray);}
                        if ($manufacturersArray) {$eventRequest->addMetaData('place_order', 'manufacturer', $manufacturersArray);}
                        if ($discounted) {$eventRequest->addMetaData('place_order', 'discounted', $discounted);}
                        if ($weight) {$eventRequest->addMetaData('place_order', 'weight', +$weight);}

                        $pointsTransaction = new \Gameball\Models\PointsTransaction();
                        $pointsTransaction->rewardAmount = $amount;
                        $pointsTransaction->transactionId = $orderId;
                        $actionRequest = \Gameball\Models\ActionRequest::factory($playerRequest, $eventRequest, $pointsTransaction);
                        $res = $gameball->action->sendAction($actionRequest);


                    }
                }
            }
        }
            else if ($this->order->getState() === 'new') {
                $couponCode = $this->order->getData('coupon_code');

                if ($couponCode) {

                    $client = $this->httpClientFactory->create();
                    $client->setUri('https://api.gameball.co/api/v1.0/Integrations/DiscountCode');
                    $client->setMethod(\Zend_Http_Client::PUT);
                    $client->setHeaders(\Zend_Http_Client::CONTENT_TYPE, 'application/json');
                    $client->setHeaders('Accept', 'application/json');

                    $headers = ["APIKey" => $this->clientKeys->getApiKey()];
                    $client->setHeaders($headers);
                    $data = [$couponCode];
                    $client->setRawData(json_encode($data));

                    $responseBody = $client->request()->getBody();
                    $bodyAsArray = json_decode($responseBody, true);
                  
                }

            }

        } catch (Exception $e) {
        }
    }


    /**
     * @method hasDiscount
     * @return void
     *
     */
    private function setIsDiscounted()
    {
        $discountAmount = $this->order->getDiscountAmount();

        if ($discountAmount === '0') {
            $discounted = 0;
        } else {
            $discounted = 1;
        }
        return $discounted;
    }

}
