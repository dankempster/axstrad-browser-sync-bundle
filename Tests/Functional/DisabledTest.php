<?php
namespace Axstrad\Bundle\BrowserSyncBundle\Tests\Functional;

use Axstrad\Bundle\UseCaseTestBundle\Test\UseCaseTest;

/**
 * Tests the bundle when it's disabled in config.yml
 */
class DisabledTest extends UseCaseTest
{
    protected static $useCase = 'bs-disabled';

    /**
     * @depends Axstrad\Bundle\BrowserSyncBundle\Tests\Functional\DefaultTest::testResponseHasBrowserSyncScript
     */
    public function testResponseNotHasBrowserSyncScript()
    {
        $client = self::createClient();
        $crawler = $client->request('GET', '/hello/dan');

        $this->assertNotRegExp(
            '/\/browser-sync-client\..*\.js/',
            $client->getResponse()->getContent(),
            'Browser Sync is disbaled, but it\'s markup is in the response'
        );
    }
}
