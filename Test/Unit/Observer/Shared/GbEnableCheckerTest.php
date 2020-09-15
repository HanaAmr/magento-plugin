<?php

namespace GbPlugin\Integration\Test\Unit\Observer\Shared;

use GbPlugin\Integration\Observer\Shared\GbEnableChecker;

use GbPlugin\Integration\Observer\Shared\ClientKeysTable;
use Magento\Framework\HTTP\Client\Curl;

class GbEnableCheckerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var GbEnableChecker
     */
    private $object;

    protected function setUp()
    {
        $ClientKeysTableMock = $this->getMockBuilder(ClientKeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $CurlMock = $this->getMockBuilder(Curl::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new GbEnableChecker($CurlMock,$ClientKeysTableMock);
    }

    public function testGbEnableCheckerInstance()
    {
        $this->assertInstanceOf(GbEnableChecker::class, $this->object);
    }

    public function testCheckFn()
    {
        $this->assertInternalType('string', $this->object->check());
    }

}
