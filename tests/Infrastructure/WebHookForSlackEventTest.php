<?php

namespace Infra\InfraBot\Tests\acceptance;

use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use Infra\InfraBot\Application\CreateTaskInJiraFromSlackMessage;
use Infra\InfraBot\Application\Messenger\Message;
use Infra\InfraBot\Application\SuccessResult;
use Infra\InfraBot\Infrastructure\WebHookForSlackEvent;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class WebHookForSlackEventTest extends WebTestCase
{

    /**
     *  Запускать только в ручную с нужными данными.
     */
    public function test1(): void
    {
        $this->markTestSkipped();
        $kernel = self::bootKernel();
        /** @var CreateTaskInJiraFromSlackMessage $client */
        $client = $kernel->getContainer()->get('Infra\InfraBot\Application\CreateTaskInJiraFromSlackMessage');

        //ивент который приходит на вебхук при создании сообщения. Вставь свои данные.
        $content = '{"token":"pwVZgPTwvl40oc6EYqRk8dS1","team_id":"T03A3SUFB","api_app_id":"A011QPZQHD2","event":{"client_msg_id":"171338cf-ef84-4b95-9037-049d18633804","type":"message","text":"lol","user":"UN3CFT97U","ts":"1587479092.003200","team":"T03A3SUFB","blocks":[{"type":"rich_text","block_id":"Z4\/pU","elements":[{"type":"rich_text_section","elements":[{"type":"text","text":"lol"}]}]}],"channel":"C011KGC6A1Y","event_ts":"1587479092.003200","channel_type":"channel"},"type":"event_callback","event_id":"Ev0121G6LN94","event_time":1587479092,"authed_users":["U011K8N0UHY"]}';

        $content = \json_decode($content, true);
        $message = Message::createFromEvent($content['event']);
        $result = $client->create($message);

        self::assertInstanceOf(SuccessResult::class, $result);
    }

//    public function test2()
//    {
//        $content = '{"token":"pwVZgPTwvl40oc6EYqRk8dS1","team_id":"T03A3SUFB","api_app_id":"A011QPZQHD2","event":{"client_msg_id":"171338cf-ef84-4b95-9037-049d18633804","type":"message","text":"lol","user":"UN3CFT97U","ts":"1587479092.003200","team":"T03A3SUFB","blocks":[{"type":"rich_text","block_id":"Z4\/pU","elements":[{"type":"rich_text_section","elements":[{"type":"text","text":"lol"}]}]}],"channel":"C011KGC6A1Y","event_ts":"1587479092.003200","channel_type":"channel"},"type":"event_callback","event_id":"Ev0121G6LN94","event_time":1587479092,"authed_users":["U011K8N0UHY"]}';
//
//        dump(\json_decode($content , true));
//        $client = new \GuzzleHttp\Client([
//            'base_uri' => 'localhost:8000',
//        ]);
//
//        $kernel = self::bootKernel();
//
//        try {
//
//            $response = $client->request('POST',
//                '/webhook/slack', [
//                'body' => $content,
//                'headers' =>[
//                    'Content-type' => 'application/json'
//                ]
//            ]);
//
//            file_put_contents(__DIR__ . '/dump.html', $response->getBody()->getContents());
//
//
//        } catch (BadResponseException $exception) {
//            file_put_contents(__DIR__ . '/dump.html', $exception->getResponse()->getBody());
//        }
//    }
}
