<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\ViewProduct\ViewProduct;
use GbPlugin\Integration\Observer\ViewProduct\ViewProductManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Customer\Model\Session;
use Magento\Catalog\Model\CategoryFactory;


class ViewProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ViewProduct
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
           
        $this->manager = new ViewProductManager($SessionMock,$ClientkeysTableMock,$CategoryFactoryMock,$GbEnableCheckerMock);
        $this->object = new ViewProduct($this->manager);
   
    }

    public function testViewProductInstance()
    {
        $this->assertInstanceOf(ViewProduct::class, $this->object);
    }

    public function testViewProductManagerInstance()
    {
        $this->assertInstanceOf(ViewProductManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
