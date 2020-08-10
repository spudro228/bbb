<?php

namespace Infra\InfraBot\Tests\Infrastructure\Slack\EventHendler;

use Infra\InfraBot\Application\Action\ResolveTask;
use Infra\InfraBot\Application\Action\Wait;
use Infra\InfraBot\Application\Action\WellNotFix;
use Infra\InfraBot\Application\Action\WorkInProgress;
use Infra\InfraBot\Infrastructure\Slack\EventHendler\ReactionAddedHandler;
use PHPUnit\Framework\TestCase;

class ReactionAddedHandlerTest extends TestCase
{

    public function testIsReactionAddedEvent(): void
    {
        $event = [
            'type' => 'reaction_added',
            'user' => 'UN3CFT97U',
            'item' => [
                'type' => 'message',
                'channel' => 'C011KGC6A1Y',
                'ts' => '1587369625.001400',
            ],
            'reaction' => 'heavy_check_mark',
            'item_user' => 'UN3CFT97U',
            'event_ts' => '1587370483.001800',
        ];

        $handler = new ReactionAddedHandler($this->createMock(ResolveTask::class));
        self::assertTrue($handler->isReactionAddedEvent($event));

        $event = [
            'type' => 'reaction_removed',
            'user' => 'UN3CFT97U',
            'item' => [
                'type' => 'message',
                'channel' => 'C011KGC6A1Y',
                'ts' => '1587369625.001400',
            ],
            'reaction' => 'heavy_check_mark',
            'item_user' => 'UN3CFT97U',
            'event_ts' => '1587370483.001800',
        ];

        self::assertFalse($handler->isReactionAddedEvent($event));

        $event = [''];
        $this->expectException(\RuntimeException::class);
        self::assertFalse($handler->isReactionAddedEvent($event));

    }

    public function testHandle()
    {
        $event = [
            'type' => 'reaction_added',
            'user' => 'UN3CFT97U',
            'item' => [
                'type' => 'message',
                'channel' => 'C011KGC6A1Y',
                'ts' => '1587369625.001400',
            ],
            'reaction' => 'heavy_check_mark',
            'item_user' => 'UN3CFT97U',
            'event_ts' => '1587370483.001800',
        ];

        $resolveTaskLogic = $this->createMock(ResolveTask::class);
        $resolveTaskLogic
            ->expects(self::once())
            ->method('resolve')
            ->with('1587369625.001400'); //todo: add channel

        $handler = new ReactionAddedHandler($resolveTaskLogic);
        $handler->handle($event);
    }


    public function testHandle_dont_handle_unsupported_reaction(): void
    {
        $event = [
            'type' => 'reaction_added',
            'user' => 'UN3CFT97U',
            'item' => [
                'type' => 'message',
                'channel' => 'C011KGC6A1Y',
                'ts' => '1587369625.001400',
            ],
            'reaction' => 'pominki',
            'item_user' => 'UN3CFT97U',
            'event_ts' => '1587370483.001800',
        ];

        $resolveTaskLogic = $this->createMock(ResolveTask::class);
        $resolveTaskLogic
            ->expects(self::never())
            ->method('resolve');

        $handler = new ReactionAddedHandler($resolveTaskLogic);
        $handler->handle($event);
    }


    /**
     * @dataProvider dataP
     */
    public function testHandleDifferentReactions(string $reaction, array $expects): void
    {

        $event = [
            'type' => 'reaction_added',
            'user' => 'UN3CFT97U',
            'item' => [
                'type' => 'message',
                'channel' => 'C011KGC6A1Y',
                'ts' => '1587369625.001400',
            ],
            'reaction' => $reaction,
            'item_user' => 'UN3CFT97U',
            'event_ts' => '1587370483.001800',
        ];

        $resolveTaskLogic = $this->createMock(ResolveTask::class);
        $resolveTaskLogic
            ->expects(self::exactly($expects[0]))
            ->method('resolve');

        $wait = $this->createMock(Wait::class);
        $wait
            ->expects(self::exactly($expects[1]))
            ->method('resolve');

        $wellNotFix = $this->createMock(WellNotFix::class);
        $wellNotFix
            ->expects(self::exactly($expects[2]))
            ->method('resolve');

        $workInProgress = $this->createMock(WorkInProgress::class);
        $workInProgress
            ->expects(self::exactly($expects[3]))
            ->method('resolve');

        $handler = new ReactionAddedHandler($resolveTaskLogic, $wait, $wellNotFix, $workInProgress);
        $handler->handle($event);
    }

    public function dataP(): array
    {
        return [
            'reaction heavy_check_mark' => [
                'heavy_check_mark', [1, 0, 0, 0],
            ],
            'reaction waiting' => [
                'waiting', [0, 1, 0, 0],
            ],
            'reaction no_entry_sign' => [
                'no_entry_sign', [0, 0, 1, 0],
            ],
            'reaction eyes' => [
                'eyes', [0, 0, 0, 1],
            ],
        ];
    }
}
