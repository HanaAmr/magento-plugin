<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\CustomerSave\CustomerSave;
use GbPlugin\Integration\Observer\CustomerSave\CustomerSaveManager;
use Magento\Framework\Event\ObserverInterface;

use Magento\Customer\Model\Customer;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;


class CustomerSaveTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CustomerSave
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

        $this->manager = new CustomerSaveManager($ClientkeysTableMock,$CustomerMock);
        $this->object = new CustomerSave($this->manager);

    }

    public function testCustomerSaveInstance()
    {
        $this->assertInstanceOf(CustomerSave::class, $this->object);
    }

    public function testCustomerSaveManagerInstance()
    {
        $this->assertInstanceOf(CustomerSaveManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
