<?php
namespace Axstrad\Bundle\BrowserSyncBundle\Tests\Functional;

use Axstrad\Bundle\UseCaseTestBundle\Test\UseCaseTest;

/**
 * Tests the bundle when it's disabled in config.yml
 */
class ClientVersionTest extends UseCaseTest
{
    protected static $useCase = 'client-version-set';

    /**
     */
    public function testResponseNotHasBrowserSyncScript()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/hello/dan');

        $this->assertRegExp(
            '/\/browser-sync-client\.1\.9\.0\.js/',
            $client->getResponse()->getContent(),
            'Versioned Browser Sync URL was not found in the response'
        );
    }
}
