<?php
/**
 * This file is part of the Axstrad library.
 *
 * (c) Dan Kempster <dev@dankempster.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dan Kempster <dev@dankempster.co.uk>
 * @package Axstrad\BrowserSyncBundle
 */

namespace Axstrad\Bundle\BrowserSyncBundle\EventListener;

use Axstrad\Bundle\BrowserSyncBundle\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig_Environment;


/**
 * Axstrad\Bundle\BrowserSyncBundle\EventListener\BrowserSyncListener
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
    protected $mode = self::ENABLED;

    /**
     * @var null|string
     */
    protected $clientVersion = null;

    /**
     * @var integer
     */
    protected $serverPort = 3000;

    /**
     * Class constructor
     *
     * @param Twig_Environment $twig
     * @param integer $mode
     */
    public function __construct(Twig_Environment $twig, $mode = self::ENABLED)
    {
        $this->twig = $twig;
        $this->mode = (integer) $mode;
    }

    /**
     * Get clientVersion
     *
     * @return string
     * @see setClientVersion
     */
    public function getClientVersion()
    {
        return $this->clientVersion;
    }

    /**
     * Set clientVersion
     *
     * @param  string $clientVersion
     * @return self
     * @see getClientVersion
     */
    public function setClientVersion($clientVersion)
    {
        $this->clientVersion = (string) $clientVersion;
        return $this;
    }

    /**
     * Get serverPort
     *
     * @return integer
     * @see setServerPort
     */
    public function getServerPort()
    {
        return $this->serverPort;
    }

    /**
     * Set serverPort
     *
     * @param  integer $serverPort
     * @return self
     * @throws InvalidArgumentException If $serverPort is not numeric
     * @see getServerPort
     */
    public function setServerPort($serverPort)
    {
        if ( ! is_numeric($serverPort)) {
            throw InvalidArgumentException::create("integer", $serverPort);
        }

        $this->serverPort = (integer) $serverPort;

        return $this;
    }

    /**
     * Get mode
     *
     * @return integer
     * @see setMode
     */
    public function getMode()
    {
        return $this->mode;
    }

    /**
     * Set mode
     *
     * @param  integer $mode
     * @return self
     * @throws InvalidArgumentException If $mode is not numeric
     * @see getMode
     */
    public function setMode($mode)
    {
        if ( ! is_numeric($mode)) {
            throw InvalidArgumentException::create(
                "integer",
                $mode
            );
        }

        $this->mode = (integer) $mode;

        return $this;
    }

    /**
     * @return boolean
     */
    public function isEnabled()
    {
        return self::ENABLED === $this->mode;
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
        if ( ! $this->isEnabled()              // bundle is disabled,
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
        }
        else {
            $posrFunction   = 'strripos';
            $substrFunction = 'substr';
        }

        $content = $response->getContent();
        $pos = $posrFunction($content, '</body>');

        if (false !== $pos) {
            $markup = "\n".$this->twig->render(
                '@AxstradBrowserSync/sync_markup.html.twig',
                array(
                    'serverPort'   => $this->serverPort,
                    'clientVersion' => $this->clientVersion,
                )
            )."\n";
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
