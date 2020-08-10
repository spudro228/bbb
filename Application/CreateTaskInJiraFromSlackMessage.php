<?php

namespace Infra\InfraBot\Application;

use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\Messenger\ReplyMessage;

class CreateTaskInJiraFromSlackMessage
{
    /**
     * @var TaskManagerInterface
     */
    private $taskManager;
    /**
     * @var MessengerInterface
     */
    private $messenger;

    public function __construct(TaskManagerInterface $taskManager, MessengerInterface $messenger)
    {
        $this->taskManager = $taskManager;
        $this->messenger = $messenger;
    }

    /**
     * @param Message $message
     * @return Result
     */
    public function create(Message $message): Result
    {
        if ($message->isFromBot() === true) {
            return new FailureResult($message, 'Message form bot.');
        }

        if ($message->hasReferenceToAnotherTask() === true) {
            return new FailureResult($message, 'Has reference to anoter task in tou body');
        }

        if ($message->isFromThread() === true) {
            return new FailureResult($message, 'Message from thread');
        }

        $permalink = $this->messenger->getPermalink($message);
        $userEmail = $this->messenger->getUserEmail($message->user());

        $text = StringHelper::removeGreetings($message->text());
        $jiraTask = $this->taskManager->createTask(
            $text,
            $text . ' ' . $permalink,
            $userEmail
        );

        $this->messenger->replyToMessage(
            $message,
            ReplyMessage::createFromJiraTask($jiraTask)
        );

        return new SuccessResult($jiraTask);
    }
}
