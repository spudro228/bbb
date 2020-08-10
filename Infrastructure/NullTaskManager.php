<?php


namespace Infra\InfraBot\Infrastructure;


use Infra\InfraBot\Application\JiraTask;
use Infra\InfraBot\Application\TaskManagerInterface;

class NullTaskManager implements TaskManagerInterface
{
    public function createTask(string $summary, string $description, string $email = null): JiraTask
    {
        return JiraTask::createRegistered('INFRADEV-1804', 'https://devjira.skyeng.ru/browse/INFRADEV-1804');
    }

    public function resolveTask(string $jiraTask): void
    {
        // TODO: Implement resolveTask() method.
    }

    public function moveToWorkInProgress(string $jiraTask): void
    {
        // TODO: Implement moveToWaiting() method.
    }

    public function moveToWontDo(string $jiraTask): void
    {
        // TODO: Implement moveToWellNotFix() method.
    }

    public function moveToWaiting($jiraTask): void
    {
        // TODO: Implement moveToWaiting() method.
    }
}
