<?php


namespace Infra\InfraBot\Infrastructure\Slack\EventHendler;


use Infra\InfraBot\Application\Action\ResolveTask;
use Infra\InfraBot\Application\Action\Wait;
use Infra\InfraBot\Application\Action\WellNotFix;
use Infra\InfraBot\Application\Action\WorkInProgress;

class ReactionAddedHandler
{
    private const HEAVY_CHECK_MARK = 'heavy_check_mark';
    private const WAITING = 'waiting';
    private const NO_ENTRY_SIGN = 'no_entry_sign';
    private const EYES = 'eyes';

    /**
     * @var ResolveTask
     */
    private $resolveTask;
    /**
     * @var Wait
     */
    private $wait;
    /**
     * @var WellNotFix
     */
    private $wellNotFix;
    /**
     * @var WorkInProgress
     */
    private $workInProgress;

    public function __construct(
        ResolveTask $resolveTask,
        Wait $wait,
        WellNotFix $wellNotFix,
        WorkInProgress $workInProgress
    )
    {
        $this->resolveTask = $resolveTask;
        $this->wait = $wait;
        $this->wellNotFix = $wellNotFix;
        $this->workInProgress = $workInProgress;
    }

    public
    function isReactionAddedEvent(array $event): bool
    {
        if (\array_key_exists('type', $event) === false) {
            throw new \RuntimeException('Invalid event data. Event "type" does not exist.');
        }

        return $event['type'] === 'reaction_added';
    }


    public function handle(array $event): void
    {
        if ($event['reaction'] === self::HEAVY_CHECK_MARK) {
            $this->resolveTask->resolve($event['item']['ts'], $event['item']['channel']);

            return;
        }

        if ($event['reaction'] === self::WAITING) {
            $this->wait->resolve($event['item']['ts'], $event['item']['channel']);

            return;
        }

        if ($event['reaction'] === self::NO_ENTRY_SIGN) {
            $this->wellNotFix->resolve($event['item']['ts'], $event['item']['channel']);

            return;
        }

        if ($event['reaction'] === self::EYES) {
            $this->workInProgress->resolve($event['item']['ts'], $event['item']['channel']);

            return;
        }
    }
}
