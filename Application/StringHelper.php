<?php

namespace Infra\InfraBot\Application;

class StringHelper
{
    public static function removeGreetings(string $text): string
    {
        $str = preg_replace('/^(Привет|Здравствуйте)\s*,*\s*/mu', '', $text);

        if ($str === null) {
            return $text;
        }

        $str = self::mb_ucfirst($str);

        return $str;
    }

    private static function mb_ucfirst($str): string
    {
        $fc = mb_strtoupper(mb_substr($str, 0, 1));
        return $fc . mb_substr($str, 1);
    }
}
