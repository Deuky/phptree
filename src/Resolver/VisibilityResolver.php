<?php

namespace PhpTree\Resolver;

use PhpParser\Node;
use PhpParser\Node\Param;

class VisibilityResolver
{
    public static function resolve(Node $node): ?string
    {
        if ($node instanceof Param && !$node->isPromoted()) {
            return null;
        }

        return match(true) {
            $node->isPublic()       => 'public',
            $node->isProtected()    => 'protected',
            $node->isPrivate()      => 'private',
            default                 => throw new \Exception,
        };
    }
}