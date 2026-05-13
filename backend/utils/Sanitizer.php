<?php

class Sanitizer
{
    public static function string(?string $s, int $maxLen = 8192): string
    {
        $s = trim((string) $s);
        if ($s === '') {
            return '';
        }
        $s = strip_tags($s);
        if (mb_strlen($s) > $maxLen) {
            $s = mb_substr($s, 0, $maxLen);
        }
        return $s;
    }

    public static function email(?string $s): string
    {
        return strtolower(trim((string) $s));
    }
}
