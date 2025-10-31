<?php

namespace WechatPayScoreBundle\Tests\Controller;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tourze\PHPUnitSymfonyWebTest\AbstractWebTestCase;
use WechatPayScoreBundle\Controller\CallbackController;

/**
 * @internal
 */
#[CoversClass(CallbackController::class)]
#[RunTestsInSeparateProcesses]
final class CallbackControllerTest extends AbstractWebTestCase
{
    protected function getTestUrl(): string
    {
        return '/wechat-payment/score-order/success-callback/test-order';
    }

    #[DataProvider('provideNotAllowedMethods')]
    public function testMethodNotAllowed(string $method): void
    {
        $client = self::createClientWithDatabase();
        $client->catchExceptions(true);
        $client->request($method, $this->getTestUrl());

        self::assertSame(405, $client->getResponse()->getStatusCode());
    }
}
