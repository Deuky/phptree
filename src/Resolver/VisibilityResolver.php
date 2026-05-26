<?php

namespace PhpTree\Resolver;

use PhpParser\Node;

class VisibilityResolver
{
    public static function resolve(Node $node): string
    {
        return match(true) {
            $node->isPublic()    => 'public',
            $node->isProtected() => 'protected',
            $node->isPrivate()   => 'private',
            default              => throw new \Exception
        };
    }
}