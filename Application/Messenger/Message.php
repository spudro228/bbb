<?php

namespace Infra\InfraBot\Application\Messenger;

class Message
{
    /**
     * @var string
     */
    private $text;

    /**
     * @var string
     */
    private $messageId;

    /**
     * @var string
     */
    private $botId;

    /** @var string */
    private $theadId;

    /**
     * @var string
     */
    private $channelId;

    /**
     * @var string
     */
    private $userId;

    private function __construct()
    {
    }

    public static function createFromArray(array $message): self
    {
        $self = new self();
        $self->text = $message['text'];
        $self->messageId = $message['ts'];
        $self->botId = $message['bot_id'] ?? null;

        return $self;
    }

    public static function createFromEvent(array $event): self
    {
        $self = self::createFromArray($event);
        $self->messageId = $event['event_ts'];
        $self->theadId = $event['thread_ts'] ?? null;
        $self->channelId = $event['channel'];
        $self->userId = $event['user'];

        return $self;
    }


    public function text(): string
    {
        return $this->text;
    }

    public function messageId(): string
    {
        return $this->messageId;
    }

    public function isFromBot(): bool
    {
        return $this->botId !== null;
    }

    public function hasReferenceToAnotherTask(): bool
    {
        return preg_match('/INFRA.*-\d*/m', $this->text) !== 0;
    }

    public function isFromThread(): bool
    {
        return $this->theadId !== null;
    }

    public function chanelId(): string
    {
        return $this->channelId;
    }

    public function user(): string
    {
        return $this->userId;
    }
}
