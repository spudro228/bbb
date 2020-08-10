<?php

namespace Infra\InfraBot\Tests\Application;

use Infra\InfraBot\Application\CreateTaskInJiraFromSlackMessage;
use Infra\InfraBot\Application\FailureResult;
use Infra\InfraBot\Application\JiraTask;
use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\Messenger\ReplyMessage;
use Infra\InfraBot\Application\SuccessResult;
use Infra\InfraBot\Application\TaskManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CreateTaskInJiraFromSlackMessageTest extends TestCase
{

    public function testCreate_success(): void
    {
        $jiraTaskAfterSave = $this->createMock(JiraTask::class);

        /** @var Message|MockObject $message */
        $message = $this->createMock(Message::class);
        $message
            ->expects($this->once())
            ->method('isFromBot')
            ->willReturn(false);
        $message
            ->expects($this->once())
            ->method('hasReferenceToAnotherTask')
            ->willReturn(false);

        $message
            ->expects(self::once())
            ->method('isFromThread')
            ->willReturn(false);

        $message
            ->expects(self::once())
            ->method('text')
            ->willReturn('Привет, я дурачок');

        $message
            ->expects(self::once())
            ->method('user')
            ->willReturn('U011K8N0UHY');

        /** @var TaskManagerInterface|MockObject $taskManager */
        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects($this->once())
            ->method('createTask')
            ->with(
                'Я дурачок',
                'Я дурачок https://skyeng.slack.com/archives/C011KGC6A1Y/p1586941273000400',
                'kek@lol.ru'
            )
            ->willReturn($jiraTaskAfterSave);

        /** @var MessengerInterface|MockObject $messenger */
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::once())
            ->method('replyToMessage')
            ->with($message, ReplyMessage::createFromJiraTask($jiraTaskAfterSave));

        $messenger
            ->expects(self::once())
            ->method('getPermalink')
            ->with($message)
            ->willReturn('https://skyeng.slack.com/archives/C011KGC6A1Y/p1586941273000400');

        $messenger
            ->expects(self::once())
            ->method('getUserEmail')
            ->with('U011K8N0UHY')
            ->willReturn('kek@lol.ru');


        $command = new CreateTaskInJiraFromSlackMessage($taskManager, $messenger);
        /** @var SuccessResult $successResult */
        $successResult = $command->create($message);

        self::assertInstanceOf(SuccessResult::class, $successResult);
        self::assertEquals($jiraTaskAfterSave, $successResult->result());
    }

    public function testCreate_fail_is_from_bot_message(): void
    {

        /** @var Message|MockObject $message */
        $message = $this->createMock(Message::class);
        $message
            ->expects(self::once())
            ->method('isFromBot')
            ->willReturn(true);
        $message
            ->expects(self::never())
            ->method('hasReferenceToAnotherTask');

        $message
            ->expects(self::never())
            ->method('isFromThread');

        $message
            ->expects(self::never())
            ->method('user');

        /** @var TaskManagerInterface|MockObject $taskManager */
        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects($this->never())
            ->method('createTask');

        /** @var MessengerInterface|MockObject $messenger */
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::never())
            ->method('replyToMessage');

        $messenger
            ->expects(self::never())
            ->method('getPermalink');

        $messenger
            ->expects(self::never())
            ->method('getUserEmail');

        $command = new CreateTaskInJiraFromSlackMessage($taskManager, $messenger);

        /** @var FailureResult $successResult */
        $successResult = $command->create($message);
        self::assertInstanceOf(FailureResult::class, $successResult);
        self::assertEquals('Message form bot.', $successResult->cause());
        self::assertEquals($message, $successResult->message());
    }


    public function testCreate_fail_message_has_ref_to_another_task(): void
    {

        /** @var Message|MockObject $message */
        $message = $this->createMock(Message::class);
        $message
            ->expects(self::once())
            ->method('isFromBot')
            ->willReturn(false);
        $message
            ->expects(self::once())
            ->method('hasReferenceToAnotherTask')
            ->willReturn(true);

        $message
            ->expects(self::never())
            ->method('isFromThread');

        $message
            ->expects(self::never())
            ->method('user');

        /** @var TaskManagerInterface|MockObject $taskManager */
        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects($this->never())
            ->method('createTask');

        /** @var MessengerInterface|MockObject $messenger */
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::never())
            ->method('replyToMessage');

        $messenger
            ->expects(self::never())
            ->method('getPermalink');

        $messenger
            ->expects(self::never())
            ->method('getUserEmail');

        $message
            ->expects(self::never())
            ->method('user');

        $command = new CreateTaskInJiraFromSlackMessage($taskManager, $messenger);

        /** @var FailureResult $successResult */
        $successResult = $command->create($message);
        self::assertInstanceOf(FailureResult::class, $successResult);
        self::assertEquals('Has reference to anoter task in tou body', $successResult->cause());
        self::assertEquals($message, $successResult->message());
    }

    public function testCreate_fail_message_if_from_thread(): void
    {

        /** @var Message|MockObject $message */
        $message = $this->createMock(Message::class);
        $message
            ->expects(self::once())
            ->method('isFromBot')
            ->willReturn(false);
        $message
            ->expects(self::once())
            ->method('hasReferenceToAnotherTask')
            ->willReturn(false);

        $message
            ->expects(self::once())
            ->method('isFromThread')
            ->willReturn(true);

        $message
            ->expects(self::never())
            ->method('user');

        /** @var TaskManagerInterface|MockObject $taskManager */
        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects($this->never())
            ->method('createTask');

        /** @var MessengerInterface|MockObject $messenger */
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::never())
            ->method('replyToMessage');

        $messenger
            ->expects(self::never())
            ->method('getPermalink');

        $messenger
            ->expects(self::never())
            ->method('getUserEmail');

        $command = new CreateTaskInJiraFromSlackMessage($taskManager, $messenger);

        /** @var FailureResult $successResult */
        $successResult = $command->create($message);
        self::assertInstanceOf(FailureResult::class, $successResult);
        self::assertEquals('Message from thread', $successResult->cause());
        self::assertEquals($message, $successResult->message());
    }

    public function testCreate_success_if_user_dont_have_email_in_slack(): void
    {
        $jiraTaskAfterSave = $this->createMock(JiraTask::class);

        /** @var Message|MockObject $message */
        $message = $this->createMock(Message::class);
        $message
            ->expects($this->once())
            ->method('isFromBot')
            ->willReturn(false);
        $message
            ->expects($this->once())
            ->method('hasReferenceToAnotherTask')
            ->willReturn(false);

        $message
            ->expects(self::once())
            ->method('isFromThread')
            ->willReturn(false);

        $message
            ->expects(self::once())
            ->method('text')
            ->willReturn('Привет, я дурачок');

        $message
            ->expects(self::once())
            ->method('user')
            ->willReturn('U011K8N0UHY');

        /** @var TaskManagerInterface|MockObject $taskManager */
        $taskManager = $this->createMock(TaskManagerInterface::class);
        $taskManager
            ->expects($this->once())
            ->method('createTask')
            ->with(
                'Я дурачок',
                'Я дурачок https://skyeng.slack.com/archives/C011KGC6A1Y/p1586941273000400',
                null
            )
            ->willReturn($jiraTaskAfterSave);

        /** @var MessengerInterface|MockObject $messenger */
        $messenger = $this->createMock(MessengerInterface::class);
        $messenger
            ->expects(self::once())
            ->method('replyToMessage')
            ->with($message, ReplyMessage::createFromJiraTask($jiraTaskAfterSave));

        $messenger
            ->expects(self::once())
            ->method('getPermalink')
            ->with($message)
            ->willReturn('https://skyeng.slack.com/archives/C011KGC6A1Y/p1586941273000400');

        $messenger
            ->expects(self::once())
            ->method('getUserEmail')
            ->with('U011K8N0UHY')
            ->willReturn(null);


        $command = new CreateTaskInJiraFromSlackMessage($taskManager, $messenger);
        /** @var SuccessResult $successResult */
        $successResult = $command->create($message);

        self::assertInstanceOf(SuccessResult::class, $successResult);
        self::assertEquals($jiraTaskAfterSave, $successResult->result());
    }
}
