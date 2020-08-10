<?php

namespace Infra\InfraBot\Tests\Application\Actions;

use Infra\InfraBot\Application\Action\ResolveTask;
use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\TaskManagerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ResolveTaskTest extends TestCase
{


    public function testResolve()
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
            ->method('resolveTask')
            ->with('113123');

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects(self::never())
            ->method('error');

        $taskResolver = new ResolveTask($messenger, $taskManager, $logger);
        $taskResolver->resolve('1337.123', 'C1234567890');
    }
}
