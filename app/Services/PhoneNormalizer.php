<?php

namespace App\Services;

class PhoneNormalizer
{
    public static function normalize(string $phone): string
    {
        return preg_replace('/\D+/', '', $phone);
    }

    public static function reverse(string $normalized): string
    {
        return strrev($normalized);
    }

    public static function isValid(string $phone): bool
    {
        $normalized = self::normalize($phone);
        return strlen($normalized) >= 3 && strlen($normalized) <= 20;
    }
}