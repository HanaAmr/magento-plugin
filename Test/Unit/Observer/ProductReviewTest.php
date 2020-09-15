<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\ProductReview\ProductReview;
use GbPlugin\Integration\Observer\ProductReview\ProductReviewManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;


class ProductReviewTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ProductReview
     */
    private $object;
    private $manager;

    protected function setUp()
    {
        $GbEnableCheckerMock = $this->getMockBuilder(GbEnableChecker::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ClientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CategoryFactoryMock = $this->getMockBuilder(CategoryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ProductFactoryMock = $this->getMockBuilder(ProductFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
           
        $this->manager = new ProductReviewManager($ClientkeysTableMock,$CategoryFactoryMock,$ProductFactoryMock,$GbEnableCheckerMock);
        $this->object = new ProductReview($this->manager);
    }

    public function testProductReviewInstance()
    {
        $this->assertInstanceOf(ProductReview::class, $this->object);
    }

    public function testProductReviewManagerInstance()
    {
        $this->assertInstanceOf(ProductReviewManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
