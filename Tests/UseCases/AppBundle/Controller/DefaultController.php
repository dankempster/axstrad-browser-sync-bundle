<?php

namespace Axstrad\Bundle\BrowserSyncBundle\Tests\UseCases\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('AxstradBrowserSyncBundleAppBundle:Default:index.html.twig', array('name' => $name));
    }
}
