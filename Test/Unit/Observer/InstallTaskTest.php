<?php

namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\InstallTask;
use Magento\Framework\Event\ObserverInterface;

use Magento\Framework\HTTP\Client\Curl;
use GbPlugin\Integration\Observer\Shared\ClientkeysTable;


class InstallTaskTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var InstallTask
     */
    private $object;

    protected function setUp()
    {
        $CurlMock = $this->getMockBuilder(Curl::class)
            ->disableOriginalConstructor()
            ->getMock();

        $ClientkeysTableMock = $this->getMockBuilder(ClientkeysTable::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new InstallTask($CurlMock, $ClientkeysTableMock);
    }

    public function testInstallTaskInstance()
    {
        $this->assertInstanceOf(InstallTask::class, $this->object);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
