<?php

namespace GbPlugin\Integration\Observer\ProductReview;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductReview implements ObserverInterface
{
  
    protected $ProductReviewManager;

    public function __construct(
        ProductReviewManager $ProductReviewManager
    ) {
        $this->ProductReviewManager = $ProductReviewManager;
    }

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $review = $observer->getDataObject();
        $this->ProductReviewManager->execute($review);
    }

}
