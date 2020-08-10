<?php

namespace Infra\InfraBot\Tests\Infrastructure;

use GuzzleHttp\ClientInterface;
use Infra\InfraBot\Infrastructure\SlackMessenger;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class SlackMessengerTest extends TestCase
{

    public function testFindJiraTaskInThread()
    {
        $data = [
            'messages' => [
                [
                    'bot_id' => '123',
                    'text' => 'Создана задача <https://devjira.skyeng.ru/browse/INFRA-14296|INFRA-14296>',
                ],
                [
                    'client_msg_id' => 'd5fcf740-6dbb-4184-a99e-eb2b8e509c40',
                    'text' => 'test test',
                ],
                [
                    'bot_id' => '321',
                    'text' => 'random text',
                ],
            ],
            'has_more' => false,
            'ok' => true,

        ];

        $response = $this->createMock(ResponseInterface::class);
        $response
            ->expects(self::once())
            ->method('getBody')
            ->willReturn(\json_encode($data));

        $client = $this->createMock(ClientInterface::class);
        $client
            ->expects(self::once())
            ->method('request')
            ->willReturn($response);
        $service = new SlackMessenger('auth_token', $client);

        self::assertEquals('INFRA-14296', $service->findJiraTaskInThread('1', '2'));
    }
}
