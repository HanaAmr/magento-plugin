<?php

namespace GbPlugin\Integration\Test\Unit\Observer\Shared;

use GbPlugin\Integration\Observer\Shared\ClientKeysTable;
use Magento\Framework\App\Helper\AbstractHelper;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ClientKeysTableTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var ClientKeysTable
     */
    private $object;

    protected function setUp()
    {
        $ScopeConfigInterfaceMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new ClientKeysTable($ScopeConfigInterfaceMock);
    }

    public function testClientKeysTableInstance()
    {
        $this->assertInstanceOf(ClientKeysTable::class, $this->object);
    }

    public function testAbstractHelperInstance()
    {
        $this->assertInstanceOf(AbstractHelper::class, $this->object);
    }

}
