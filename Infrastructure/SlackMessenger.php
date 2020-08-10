<?php

namespace Infra\InfraBot\Infrastructure;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Middleware;
use Infra\InfraBot\Application\Messenger\Message as MessageFromSalack;
use Infra\InfraBot\Application\Messenger\MessengerInterface;
use Infra\InfraBot\Application\Messenger\NotFoundJiraTaskInThread;
use Infra\InfraBot\Application\Messenger\ReplyMessage;

class SlackMessenger implements MessengerInterface
{
    private $slackClient;
    /**
     * @var string
     */
    private $slackAuthorizationToken;

    public function __construct(string $slackAuthorizationToken, ClientInterface $client)
    {
        //todo: можно отрефакторить клиент
        $this->slackClient = $client;
        $this->slackAuthorizationToken = $slackAuthorizationToken;
    }

    /**
     * @param MessageFromSalack $replayTo
     * @param ReplyMessage $replyMessage
     * @throws ClientException
     */
    public function replyToMessage(MessageFromSalack $replayTo, ReplyMessage $replyMessage): void
    {

        $data = \json_encode(
            [
                'channel' => 'C011KGC6A1Y',
                'thread_ts' => $replayTo->messageId(),
                'text' => $replyMessage->text(),
                'blocks' => [[
                    'type' => 'section',
                    'text' => [
                        'type' => 'mrkdwn',
                        'text' => $replyMessage->text(),
                    ],
                ]],
            ]
        );

        //todo: таки сделать вывод ошибок
        $response = $this->slackClient->request(
            'POST',
            'https://slack.com/api/chat.postMessage',
            [
                'http_errors' => true,
                'body' => $data,
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->slackAuthorizationToken,
                    'Content-type' => 'application/json',
                ],
            ]
        );
    }

    public function getPermalink(MessageFromSalack $message): string
    {
        $data = [
            'token' => $this->slackAuthorizationToken,
            'channel' => $message->chanelId(),
            'message_ts' => $message->messageId(),
        ];

        //todo: таки сделать вывод ошибок
        $response = $this->slackClient->request(
            'GET',
            'https://slack.com/api/chat.getPermalink',
            [
                'http_errors' => true,
                'query' => $data,
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );

        return \json_decode($response->getBody()->getContents(), true)['permalink'];
    }

    public function getUserEmail(string $userId): ?string
    {
        $data = [
            'token' => $this->slackAuthorizationToken,
            'user' => $userId,
        ];

        //todo: таки сделать вывод ошибок
        $response = $this->slackClient->request(
            'GET',
            'https://slack.com/api/users.info',
            [
                'http_errors' => true,
                'query' => $data,
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );

        $contentJson = \json_decode($response->getBody()->getContents(), true);

        return $contentJson['user']['profile']['email'] ?? null;
    }

    public function findJiraTaskInThread(string $threadId, string $channel): string
    {
        $data = [
            'token' => $this->slackAuthorizationToken,
            'channel' => $channel,
            'ts' => $threadId,
        ];

        $response = $this->slackClient->request(
            'GET',
            'https://slack.com/api/conversations.replies',
            [
                'http_errors' => true,
                'query' => $data,
                'headers' => [
                    'Content-type' => 'application/x-www-form-urlencoded',
                ],
            ]
        );

        $data = \json_decode((string)$response->getBody(), true);

        $formBotMessages = \array_filter($data['messages'], function (array $message) {
            return \array_key_exists('bot_id', $message);
        });


        foreach ($formBotMessages as $formBotMessage) {

            $text = $formBotMessage['text'];
            $matchedCount = preg_match('/\|(INFRA-\d*)/m', $text, $matches);
            if ($matchedCount === 0) {
                continue;
            }

            return $matches[1];
        }


        throw new NotFoundJiraTaskInThread('Ни в ожном сообщении от бота не найденно ссылки на задачу.');
    }
}
