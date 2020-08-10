<?php

namespace Infra\InfraBot\Infrastructure;

use Infra\InfraBot\Application\JiraTask;
use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Application\TaskManagerInterface;
use JiraRestApi\Issue\IssueField;
use JiraRestApi\Issue\IssueService;
use JiraRestApi\Issue\IssueStatus;
use JiraRestApi\Issue\Transition;
use JiraRestApi\JiraException;
use JiraRestApi\User\User;
use JiraRestApi\User\UserService;
use JsonMapper_Exception;
use Psr\Log\LoggerInterface;

class JiraTaskManager implements TaskManagerInterface
{
    //https://devjira.skyeng.ru/rest/api/2/resolution
    private const RESOLUTION_WONT_FIX_ID = '2';
    private const RESOLUTION_DONE_ID = '10100';

    /**
     * @var IssueService
     */
    private $issueService;

    /**
     * @var UserService
     */
    private $userService;
    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(
        IssueService $issueService,
        UserService $userService,
        LoggerInterface $logger
    )
    {
        $this->issueService = $issueService;
        $this->userService = $userService;
        $this->logger = $logger;
    }

    /**
     * @param string $summary
     * @param string $description
     * @param string $email
     * @return JiraTask
     * @throws JiraException
     * @throws JsonMapper_Exception
     */
    public function createTask(string $summary, string $description, string $email = null): JiraTask
    {
        $issueField = new IssueField();
        $issueField
            ->setProjectKey('INFRA') //todo: project key
            ->setSummary($summary) // убрать "привет"
            ->setDescription($description);

        if ($email !== null && ($user = $this->findUserByEmail($email)) !== null) {
            //можно использовать $user->name, но кажется что name  могут менять в отличии от key
            $issueField->setReporterName($user->key);
        }

        $issueField
            ->setPriorityName('Critical')
            ->setIssueType('Issue')
            ->setDescription($description);

        $ret = $this->issueService->create($issueField);

        return JiraTask::createRegistered(
            $ret->key,
            'https://devjira.skyeng.ru/browse/' . $ret->key
        );
    }

    public function findUserByEmail(string $email): ?User
    {
        $users = $this->userService->findUsers([
            'username' => $email,
        ]);

        if (\count($users) > 1) {
            $this->logger->error(
                "Найдено несколько пользователей с почтой {$email}. Установите автора таски в ручную."
            );
        }

        return $users[0] ?? null;
    }

    public function resolveTask(string $jiraTask): void
    {
        $transition = new Transition();
        $transition->setTransitionName('Resolved');
        $transition->setCommentBody('Resolved auto.');
        $transition->fields = [
//            'customfield_16217' => 'Done.',
            'resolution' => ['id' => self::RESOLUTION_DONE_ID],

        ];

        $this->issueService->transition($jiraTask, $transition);
    }

    public function moveToWorkInProgress(string $jiraTask): void
    {
        $transition = new Transition();
        $transition->setTransitionName('In Progress');

        $this->issueService->transition($jiraTask, $transition);
    }

    public function moveToWontDo(string $jiraTask): void
    {
        $transition = new Transition();
        $transition->setTransitionName('Resolved');
        $transition->fields = [
            'resolution' => ['id' => self::RESOLUTION_WONT_FIX_ID],
        ];

        $this->issueService->transition($jiraTask, $transition);
    }

    public function moveToWaiting(string $jiraTask): void{
        {
            $transition = new Transition();
            $transition->setTransitionName('Waiting');

            $this->issueService->transition($jiraTask, $transition);
        }
    }
}
