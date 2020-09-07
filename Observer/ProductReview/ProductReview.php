<?php

namespace GbPlugin\Integration\Observer\ProductReview;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductReview implements ObserverInterface
{
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
    /**

     * Below is the method that will fire whenever the event runs!

     *

     * @param Observer $observer

     */

    public function execute(Observer $observer)
    {
        $review = $observer->getDataObject();
        $manager = new ProductReviewManager($review,
        $this->clientKeys,
        $this->categoryFactory,
        $this->productFactory,
        $this->gbEnableChecker);
        $manager->execute();
    }

}
