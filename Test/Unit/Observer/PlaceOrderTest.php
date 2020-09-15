<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\PlaceOrder\PlaceOrder;
use GbPlugin\Integration\Observer\PlaceOrder\PlaceOrderManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Framework\HTTP\ZendClientFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;


class PlaceOrderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var PlaceOrder
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

        $ZendClientFactoryMock = $this->getMockBuilder(ZendClientFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CategoryFactoryMock = $this->getMockBuilder(CategoryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ProductFactoryMock = $this->getMockBuilder(ProductFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
           
        $this->manager = new PlaceOrderManager($ZendClientFactoryMock,$ClientkeysTableMock,$CategoryFactoryMock,$ProductFactoryMock,$GbEnableCheckerMock);
        $this->object = new PlaceOrder($this->manager);
    
    }

    public function testPlaceOrderInstance()
    {
        $this->assertInstanceOf(PlaceOrder::class, $this->object);
    }

    public function testPlaceOrderManagerInstance()
    {
        $this->assertInstanceOf(PlaceOrderManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
