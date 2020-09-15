<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\OrderRefund\OrderRefund;
use GbPlugin\Integration\Observer\OrderRefund\OrderRefundManager;
use Magento\Framework\Event\ObserverInterface;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;


class OrderRefundTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var OrderRefund
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

        $this->manager = new OrderRefundManager($ClientkeysTableMock,$GbEnableCheckerMock);
        $this->object = new OrderRefund($this->manager);
        
    }

    public function testOrderRefundInstance()
    {
        $this->assertInstanceOf(OrderRefund::class, $this->object);
    }

    public function testOrderRefundManagerInstance()
    {
        $this->assertInstanceOf(OrderRefundManager::class, $this->manager);
    }


    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
