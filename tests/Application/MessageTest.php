<?php

namespace Infra\InfraBot\Tests\Application;

use Infra\InfraBot\Application\Messenger\Message;
use PHPUnit\Framework\TestCase;

class MessageTest extends TestCase
{

    public function testCreateFromArray(): void
    {
        $message = Message::createFromArray([
            'text' => 'Zdarova, che tam s https://devjira.skyeng.ru/browse/INFRADEV-1804 ',
            'ts' => '12312312.2123',
        ]);


        self::assertEquals('Zdarova, che tam s https://devjira.skyeng.ru/browse/INFRADEV-1804 ', $message->text());
        self::assertEquals('12312312.2123', $message->messageId());
    }

//    public function testIsFromBot()
//    {
//
//    }

    public function testHasReferenceToAnotherTask(): void
    {
        $message = Message::createFromArray([
            'text' => 'Zdarova, che tam s https://devjira.skyeng.ru/browse/INFRADEV-1804 ',
            'ts' => '',
        ]);

        self::assertTrue($message->hasReferenceToAnotherTask());
    }
}
