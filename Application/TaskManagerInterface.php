<?php


namespace Infra\InfraBot\Application;


use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Infrastructure\JiraTaskManager;
use JiraRestApi\Issue\Transition;

interface TaskManagerInterface
{

    public function createTask(string $summary, string $description, string $email = null): JiraTask;

    public function resolveTask(string $jiraTask): void;

    public function moveToWorkInProgress(string $jiraTask): void;

    public function moveToWontDo(string $jiraTask): void;

    public function moveToWaiting(string $jiraTask): void;
}
