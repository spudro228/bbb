<?php


namespace Infra\InfraBot\Application\Action;


use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\Messenger\NotFoundJiraTaskInThread;
use Infra\InfraBot\Application\TaskManagerInterface;
use Psr\Log\LoggerInterface;

class WorkInProgress
{

    /**
     * @var MessengerInterface
     */
    private $messenger;
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(MessengerInterface $messenger, TaskManagerInterface $taskManager, LoggerInterface $logger)
    {
        $this->messenger = $messenger;
        $this->taskManager = $taskManager;
        $this->logger = $logger;
    }


    public function resolve(string $threadId, string $channel): void
    {
        try {
            $jiraTask = $this->messenger->findJiraTaskInThread($threadId, $channel);
        } catch (NotFoundJiraTaskInThread $e) {
            $this->logger->error('Немогу передвинуть задачу в статус Waiting, т.к. она не была найдена в треде обращения.
             Если задача существует, закройте ее руками.');

            return;
        }

        $this->taskManager->moveToWorkInProgress($jiraTask);
    }

}
