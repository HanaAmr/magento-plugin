<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\RegisterSuccess\RegisterSuccess;
use GbPlugin\Integration\Observer\RegisterSuccess\RegisterSuccessManager;
use Magento\Framework\Event\ObserverInterface;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Request\Http;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;
use Magento\Customer\Model\Customer;



class RegisterSuccessTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RegisterSuccess
     */
    private $object;
    private $manager;

    protected function setUp()
    {
        $SessionManagerInterfaceMock = $this->getMockBuilder(SessionManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ClientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CustomerRepositoryInterfaceMock = $this->getMockBuilder(CustomerRepositoryInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $HttpMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CustomerMock = $this->getMockBuilder(Customer::class)
            ->disableOriginalConstructor()
            ->getMock();
           
        $this->manager = new RegisterSuccessManager($CustomerRepositoryInterfaceMock,$SessionManagerInterfaceMock,$HttpMock,$ClientkeysTableMock,$CustomerMock);
        $this->object = new RegisterSuccess($this->manager);
  
    }

    public function testRegisterSuccessInstance()
    {
        $this->assertInstanceOf(RegisterSuccess::class, $this->object);
    }

    public function testRegisterSuccessManagerInstance()
    {
        $this->assertInstanceOf(RegisterSuccessManager::class, $this->manager);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
