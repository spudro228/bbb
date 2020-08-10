<?php

namespace Infra\InfraBot\Application\Messenger;

interface MessengerInterface
{

    public function replyToMessage(Message $replayTo, ReplyMessage $replyMessage);

    public function getPermalink(Message $message): string;

    public function getUserEmail(string $string): ?string;

    /**
     * @throws NotFoundJiraTaskInThread
     */
    public function findJiraTaskInThread(string $threadId, string $channel): string;
}
