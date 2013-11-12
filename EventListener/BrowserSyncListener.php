<?php
namespace Axstrad\BrowserSyncBundle\EventListener;

/**
 * Dependancies
 */
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


/**
 * Axstrad\BrowserSync\EventListener\BrowserSyncListener
 *
 * BrowserSyncListener injects the Browser Sync markup into the response HTML.
 */
class BrowserSyncListener implements EventSubscriberInterface
{
    const DISABLED = 1;
    const ENABLED  = 2;

    /**
     * @var Twig_Environment
     */
    protected $twig;

    /**
     * The bundle's mode.
     *
     * Use class costants (DISABLED, ENABLED) when asserting the variable value.
     *
     * @var integer
     */
    protected $mode;

    /**
     * @var string
     */
    protected $serverIp;

    /**
     * Class constructor
     *
     * @param Twig_Environment $twig
     * @param string           $serverIp
     * @param integer          $mode
     */
    public function __construct(\Twig_Environment $twig, $serverIp, $mode = self::ENABLED)
    {
        $this->twig     = $twig;
        $this->serverIp = (string) $serverIp;
        $this->mode     = (integer) $mode;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return self::DISABLED !== $this->mode;
    }

    /**
     * @param  FilterResponseEvent $event
     * @return void
     */
    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $response = $event->getResponse();
        $request = $event->getRequest();

        // don't do anything if...
        if ($this->mode === self::DISABLED              // bundle is disabled,
            || $request->isXmlHttpRequest()             // request is XML HTTP,
            || $response->isRedirect()                  // response is redurect,
            || ($response->headers->has('Content-Type') // response content is not HTML, Or
                && false === strpos($response->headers->get('Content-Type'), 'html')
            )
            || 'html' !== $request->getRequestFormat()  // Requested format is not HTML
        ) {
            return;
        }


        $this->injectMarkup($response);
    }

    /**
     * @param void
     */
    protected function injectMarkup(Response $response)
    {
        if (function_exists('mb_stripos')) {
            $posrFunction   = 'mb_strripos';
            $substrFunction = 'mb_substr';
        } else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $markup = "\n".str_replace("\n", '', $this->twig->render(
                '@AxstradBrowserSync/sync_markup.html.twig',
                array(
                    'serverIp' => $this->serverIp,
                )
            ))."\n";
            $content = $substrFunction($content, 0, $pos).$markup.$substrFunction($content, $pos);
            $response->setContent($content);
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::RESPONSE => array('onKernelResponse', -130),
        );
    }
}
