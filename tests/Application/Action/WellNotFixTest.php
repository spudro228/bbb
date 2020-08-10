<?php

namespace Infra\InfraBot\Tests\Application\Action;

use Infra\InfraBot\Application\Action\WorkInProgress;
use Infra\InfraBot\Application\Action\WellNotFix;
use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\TaskManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class WellNotFixTest extends TestCase
{


    public function testResolve(): void
    {
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::once())
            ->method('findJiraTaskInThread')
            ->with('1337.123', 'C1234567890')
            ->willReturn('113123');

        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects(self::once())
            ->method('moveToWontDo')
            ->with('113123');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error');


        $service = new WellNotFix($messenger, $taskManager, $logger);
        $service->resolve('1337.123', 'C1234567890');
    }
}
