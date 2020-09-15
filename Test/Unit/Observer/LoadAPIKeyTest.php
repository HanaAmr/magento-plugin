<?php


namespace GbPlugin\Integration\Test\Unit\Observer;

use GbPlugin\Integration\Observer\LoadAPIKey;
use Magento\Framework\Event\ObserverInterface;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\App\Request\Http;

class LoadAPIKeyTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var LoadAPIKey
     */
    private $object;

    protected function setUp()
    {
        $sessionMock = $this->getMockBuilder(SessionManagerInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpMock = $this->getMockBuilder(Http::class)
            ->disableOriginalConstructor()
            ->getMock();    

        $this->object = new LoadAPIKey($sessionMock, $httpMock);
    }

    public function testLoadAPIKeyInstance()
    {
        $this->assertInstanceOf(LoadAPIKey::class, $this->object);
    }

    public function testImplementsObserverInterface()
    {
        $this->assertInstanceOf(ObserverInterface::class, $this->object);
    }
}
