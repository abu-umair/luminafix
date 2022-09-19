<?php

namespace App\Helpers;

class Initials
{
    public static function generate(string $phrase): string
    {
        $words = explode(' ', $phrase);

        if (count($words) < 2)
            return static::makeInitialsFromSingleWord($phrase);

        return mb_strtoupper(
            mb_substr($words[0], 0, 1, 'UTF-8') .
                mb_substr(end($words), 0, 1, 'UTF-8'),
            'UTF-8'
        );
    }

    public static function makeInitialsFromSingleWord(string $phrase): string
    {
        preg_match_all('#([A-Z]+)#', $phrase, $capitals);

        if (count($capitals[1]) >= 2)
            return mb_substr(implode('', $capitals[1]), 0, 2, 'UTF-8');

        return mb_strtoupper(mb_substr($phrase, 0, 2, 'UTF-8'), 'UTF-8');
    }
}
