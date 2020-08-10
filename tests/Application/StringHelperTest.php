<?php


namespace Infra\InfraBot\Tests\Application;


use Infra\InfraBot\Application\StringHelper;
use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{

    /**
     * @dataProvider getTextWitGreetings
     */
    public function testRemoveGreetings($actual, $expected): void
    {
        self::assertEquals($expected, StringHelper::removeGreetings($actual), $actual);
    }

    public function getTextWitGreetings(): array
    {
        return [
            ['Привет, вот задачка', 'Вот задачка'],
            ['Привет , вот задачка', 'Вот задачка'],
            ['Привет,вот задачка', 'Вот задачка'],
            ['Привет ,вот задачка', 'Вот задачка'],
            ['Привет вот задачка', 'Вот задачка'],
//            ['Приветвот задачка', 'Вот задачка'],
            ['Здравствуйте, вот задачка', 'Вот задачка'],
            ['Здравствуйте , вот задачка', 'Вот задачка'],
            ['Здравствуйте ,вот задачка', 'Вот задачка'],
            ['Здравствуйте,вот задачка', 'Вот задачка'],
            ['Здравствуйтевот задачка', 'Вот задачка'],
            ['Вот задачка', 'Вот задачка'],
        ];
    }
}
