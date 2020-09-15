<?php

namespace GbPlugin\Integration\Test\Unit\Block;

use GbPlugin\Integration\Block\LoadAPIKey;

use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;

class LoadAPIKeyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LoadAPIKey
     */
    private $object;
    
    protected function setUp()
    {
        $contextMock = $this->getMockBuilder(Context::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sessionMock = $this->getMockBuilder(Session::class)
            ->disableOriginalConstructor()
            ->getMock();
            
        $clientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new LoadAPIKey($contextMock,$sessionMock,$clientkeysTableMock);
    }

    public function testLoadAPIKeyInstance()
    {
        $this->assertInstanceOf(LoadAPIKey::class, $this->object);
    }

    public function testLoadAPIKeyGetSessionFn()
    {
        $this->assertInstanceOf(Session::class, $this->object->getCustomerSession());
    }
}
