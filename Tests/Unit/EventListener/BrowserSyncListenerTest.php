<?php
namespace Axstrad\Bundle\BrowserSyncBundle\Tests\Unit\EventListener;

use Axstrad\Bundle\BrowserSyncBundle\EventListener\BrowserSyncListener;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

/**
 */
class BrowserSyncListenerTest extends \PHPUnit_Framework_TestCase
{
    protected $fixture = null;
    protected $mockRenderer;
    protected $stubEvent;
    protected $stubResponse;
    protected $stubRequest;

    public function setUp()
    {
        // Set up the stub Request
        $this->stubRequest = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->stubRequest
            ->expects($this->any())
            ->method('isXmlHttpRequest')
            ->will($this->returnValue(false))
        ;
        $this->stubRequest
            ->expects($this->any())
            ->method('getRequestFormat')
            ->will($this->returnValue('html'))
        ;


        // Set up the stub Response
        // We're using a real object because the fixture changes the object
        // content and that's difficult to mock without coupling the test with
        // the real output of the bundle's template.
        $this->stubResponse = new Response(
            '<html><head></head><body>Hello World</body></html>',
            200,
            array(
                'Content-Type' => 'text/html'
            )
        );


        // Set Up the stub Event
        $this->stubEvent = $this->getMockBuilder('Symfony\Component\HttpKernel\Event\FilterResponseEvent')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->stubEvent
            ->expects($this->any())
            ->method('getResponse')
            ->will($this->returnValue($this->stubResponse))
        ;
        $this->stubEvent
            ->expects($this->any())
            ->method('getRequest')
            ->will($this->returnValue($this->stubRequest))
        ;


        // Set up the stub Renderer
        $this->mockRenderer = $this->getMockBuilder('Twig_Environment')
            ->disableOriginalConstructor()
            ->getMock()
        ;
        $this->mockRenderer->expects($this->any())
            ->method('render')
            ->with(
                $this->equalTo('@AxstradBrowserSync/sync_markup.html.twig'),
                $this->anything()
            )
            ->will($this->returnValue('<!-- THE-INJECTED-SCRIPT -->'))
        ;


        // Set up fixture
        $this->fixture = new BrowserSyncListener(
            $this->mockRenderer
        );
    }

    public function testSubscribesToOnKernelResponseEvents()
    {
        $this->assertTrue(
            array_key_exists(
                KernelEvents::RESPONSE,
                $this->fixture->getSubscribedEvents()
            ),
            'Browser Sync listener is not subscribing to KernelEvents::RESPONSE'.
            ' events.'
        );
    }

    public function testInjectsScriptForMasterResponses()
    {
        $this->stubEvent
            ->expects($this->any())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::MASTER_REQUEST))
        ;

        $this->fixture->onKernelResponse($this->stubEvent);

        $this->assertEquals(
            "<html><head></head><body>Hello World\n".
            "<!-- THE-INJECTED-SCRIPT -->\n".
            "</body></html>",
            $this->stubResponse->getContent()
        );
    }

    public function testIgnoresHtmlSubResponses()
    {
        $this->stubEvent
            ->expects($this->any())
            ->method('getRequestType')
            ->will($this->returnValue(HttpKernelInterface::SUB_REQUEST))
        ;

        $this->fixture->onKernelResponse($this->stubEvent);

        $this->assertEquals(
            "<html><head></head><body>Hello World</body></html>",
            $this->stubResponse->getContent()
        );
    }
}
