<?php


namespace Infra\InfraBot\Application\Messenger;


use Infra\InfraBot\Application\JiraTask;

class ReplyMessage
{
    /**
     * @var string
     */
    private $textMessage;

    protected function __construct()
    {
    }


    public static function createWithText(string $textMessage): self
    {
        $self = new self();
        $self->textMessage = $textMessage;

        return $self;
    }

    public static function createFromJiraTask(JiraTask $jiraTask): self
    {
        return self::createWithText("Создана задача <{$jiraTask->taskLink()}|{$jiraTask->id()}>");
    }

    public function text(): string
    {
        return $this->textMessage;
    }
}
