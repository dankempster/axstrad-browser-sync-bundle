<?php
namespace Axstrad\Bundle\BrowserSyncBundle\Tests\Functional;

use Axstrad\Bundle\UseCaseTestBundle\Test\UseCaseTest;

/**
 * Tests the bundle with the default settings.
 *
 */
class DefaultTest extends UseCaseTest
{
    /**
     */
    public function testResponseHasBrowserSyncScript()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/hello/dan');

        $this->assertRegExp(
            '/\/browser-sync-client.*\.js/',
            $client->getResponse()->getContent(),
            'The Browser-Sync script was not part of the response'
        );
    }

    /**
     * @depends testResponseHasBrowserSyncScript
     */
    public function testInjectedScriptIsNotVersioned()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/hello/dan');

        $this->assertRegExp(
            '/\/browser-sync-client\.js/',
            $client->getResponse()->getContent(),
            'Version should not be part of injected script\'s URL'.
            ' for default configuration.'
        );
    }
}
