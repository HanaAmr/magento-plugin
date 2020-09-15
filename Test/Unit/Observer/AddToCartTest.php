<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\AddToCart\AddToCart;
use GbPlugin\Integration\Observer\AddToCart\AddToCartManager;
use Magento\Framework\Event\ObserverInterface;

use Magento\Customer\Model\Session;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Catalog\Model\CategoryFactory;
use GbPlugin\Integration\Observer\Shared\GbEnableChecker;

class AddToCartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var AddToCart
     */
    private $object;
    private $manager;

    protected function setUp()
    {
        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
                    
        $ClientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $CategoryFactoryMock = $this->getMockBuilder(CategoryFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $GbEnableCheckerMock = $this->getMockBuilder(GbEnableChecker::class)
            ->disableOriginalConstructor()
            ->getMock();    

        $this->manager = new AddToCartManager($sessionMock, $ClientkeysTableMock,$CategoryFactoryMock,$GbEnableCheckerMock);
        $this->object = new AddToCart($this->manager);
    }

    public function testAddToCartInstance()
    {
        $this->assertInstanceOf(AddToCart::class, $this->object);
    }

    public function testAddToCartManagerInstance()
    {
        $this->assertInstanceOf(AddToCartManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
