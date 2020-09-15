<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\CustomerLogin\CustomerLogin;
use GbPlugin\Integration\Observer\CustomerLogin\CustomerLoginManager;
use Magento\Framework\Event\ObserverInterface;

use Magento\Customer\Model\Customer;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;


class CustomerLoginTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomerLogin
     */
    private $object;
    private $manager;

    protected function setUp()
    {
        $CustomerMock = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ClientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->manager = new CustomerLoginManager($ClientkeysTableMock,$CustomerMock);
        $this->object = new CustomerLogin($this->manager);

    }

    public function testCustomerLoginInstance()
    {
        $this->assertInstanceOf(CustomerLogin::class, $this->object);
    }

    public function testCustomerLoginManagerInstance()
    {
        $this->assertInstanceOf(CustomerLoginManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
