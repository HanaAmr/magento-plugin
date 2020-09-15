<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\ViewCart\ViewCart;
use GbPlugin\Integration\Observer\ViewCart\ViewCartManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Cart;


class ViewCartTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ViewCart
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

        $CartMock = $this->getMockBuilder(Cart::class)
            ->disableOriginalConstructor()
            ->getMock();
           
        $this->manager = new ViewCartManager($ClientkeysTableMock,$GbEnableCheckerMock,$SessionMock,$CartMock);
        $this->object = new ViewCart($this->manager);
    }

    public function testViewCartInstance()
    {
        $this->assertInstanceOf(ViewCart::class, $this->object);
    }

    public function testViewCartManagerInstance()
    {
        $this->assertInstanceOf(ViewCartManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
