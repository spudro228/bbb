<?php

namespace Infra\InfraBot\Infrastructure;

use Infra\InfraBot\Application\Action\ResolveTask;
use Infra\InfraBot\Application\CreateTaskInJiraFromSlackMessage;
use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Infrastructure\Slack\EventHendler\ReactionAddedHandler;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class WebHookForSlackEvent
{
    /**
     * @var string
     */
    private $slackToken;

    public function __construct(string $slackWebHookToken)
    {
        $this->slackToken = $slackWebHookToken;
    }

    public function __invoke(Request $request,
                             CreateTaskInJiraFromSlackMessage $createTaskInJiraFromSlackMessage,
                             ReactionAddedHandler $reactionAddedHandler
    )
    {
        $content = $request->getContent();

        $contentJson = \json_decode($content, true);

        if ($contentJson === null) {
            throw new BadRequestHttpException('Empty content.');
        }

        if (\array_key_exists('token', $contentJson) && $contentJson['token'] !== $this->slackToken) {
            throw new AccessDeniedHttpException('Invalid slack token');
        }

        if (\array_key_exists('type', $contentJson) && $contentJson['type'] === 'url_verification') {
            $challenge = $contentJson['challenge'];

            return new JsonResponse(['challenge' => $challenge], Response::HTTP_OK);
        }


        if (\array_key_exists('type', $contentJson) && $contentJson['type'] !== 'event_callback') {
            throw new BadRequestHttpException(
                "Accept only slack event_callback, but passed {$contentJson['BadRequestHttpException']}"
            );
        }

        if ($reactionAddedHandler->isReactionAddedEvent($contentJson['event'])) {
            $reactionAddedHandler->handle($contentJson['event']);

            return new Response();
        }

        //todo:  можно убрать в отдельный хендлер
        $message = Message::createFromEvent($contentJson['event']);
        $createTaskInJiraFromSlackMessage->create($message);

        return new Response();
    }
}
