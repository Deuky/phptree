<?php

namespace PhpTree\Resolver;

use PhpParser\Node;
use str_starts_with, array_reduce, array_filter, implode, explode, trim;

class DocBlockResolver
{
    public static function extractDescription(Node $node): ?string
    {
        $doc = $node->getDocComment();

        if ($doc === null) {
            return null;
        }

        $lines = explode("\n", $doc->getText());

        $return = [];

        foreach ($lines as $line) {
            $line = trim($line, " \t/*");

            if (str_starts_with($line, '@')) {
                break;
            }

            $return[] = $line;
        }

        return implode(PHP_EOL, 
            array_filter($return)
        );
    }

    /**
     * @return string[]
     */
    public static function extractThrows(Node $node): array
    {
        $doc = $node->getDocComment();

        if ($doc === null) {
            return [];
        }

        $throws = [];
        $lines  = explode("\n", $doc->getText());

        $throws = array_reduce(
                $lines, 
                function($carry, $line) {
                    $line = trim($line, " \t/*");

                    if (str_starts_with($line, '@throws')) {
                        $carry[] = $line;
                    }

                    return $carry;
                },
                $throws
            );

        $return = [];

        foreach ($throws as $throw) {
            $parts = preg_split('/\s+/', $throw, 3);

            if (isset($parts[1]) && $parts[1] !== '') {
                $return[] = $parts[1];
            }
        }

        return $return;
    }
}