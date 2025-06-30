<?php

declare(strict_types=1);

namespace WechatPayScoreBundle\Tests\Controller;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use WechatPayScoreBundle\Controller\CallbackController;
use WechatPayScoreBundle\Entity\ScoreOrder;
use WechatPayScoreBundle\Event\ScoreOrderCallbackEvent;
use WechatPayScoreBundle\Repository\ScoreOrderRepository;

class CallbackControllerTest extends TestCase
{
    private CallbackController $controller;
    private ScoreOrderRepository $scoreOrderRepository;
    private EventDispatcherInterface $eventDispatcher;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->scoreOrderRepository = $this->createMock(ScoreOrderRepository::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        
        $this->controller = new CallbackController(
            $this->scoreOrderRepository,
            $this->eventDispatcher,
            $this->logger
        );
    }

    public function testInvokeThrowsNotFoundExceptionWhenScoreOrderNotFound(): void
    {
        $this->scoreOrderRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['outTradeNo' => 'test123'])
            ->willReturn(null);

        $request = new Request();

        $this->expectException(NotFoundHttpException::class);
        
        $this->controller->__invoke('test123', $request);
    }

}