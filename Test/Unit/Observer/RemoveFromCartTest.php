<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\RemoveFromCart\RemoveFromCart;
use GbPlugin\Integration\Observer\RemoveFromCart\RemoveFromCartManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;


class RemoveFromCartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RemoveFromCart
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

        $SessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CategoryFactoryMock = $this->getMockBuilder(CategoryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ProductFactoryMock = $this->getMockBuilder(ProductFactory::class)
            ->disableOriginalConstructor()
            ->getMock();
           
        $this->manager = new RemoveFromCartManager($SessionMock,$ClientkeysTableMock,$CategoryFactoryMock,$ProductFactoryMock,$GbEnableCheckerMock);
        $this->object = new RemoveFromCart($this->manager);
    
    }

    public function testRemoveFromCartInstance()
    {
        $this->assertInstanceOf(RemoveFromCart::class, $this->object);
    }

    public function testRemoveFromCartManagerInstance()
    {
        $this->assertInstanceOf(RemoveFromCartManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
