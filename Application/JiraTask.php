<?php


namespace Infra\InfraBot\Application;


use Infra\InfraBot\Application\Messenger\Message;

class JiraTask
{

    /** @var Message */
    private $message;

    /** @var string */
    private $id;

    /** @var string */
    private $taskLink;

    /** @var string */
    private $permalink;

    private function __construct()
    {
    }

    public static function createRegistered(string $id, string $taskLink): self
    {
        $self = new self();
        $self->id = $id;
        $self->taskLink = $taskLink;

        return $self;
    }

    public function id(): string
    {
        return $this->id;
    }

    public function taskLink(): string
    {
        return $this->taskLink;
    }

}
