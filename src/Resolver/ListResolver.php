<?php

namespace PhpTree\Resolver;

class ListResolver
{
    public static function resolve(?string $string): array
    {
        if ($string === null || $string === '') {
            return [];
        }

        return array_map(
            trim(...),
            explode(',', $string),
        );
    }
}