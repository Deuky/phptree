<?php

namespace PhpTree\Resolver;

use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\NullableType;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\UnionType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;

class TypeResolver
{
    public static function resolve(?Node $type): ?string
    {
        if ($type === null) {
            return null;
        }
        
        return match(true) {
            $type instanceof NullableType     => 'null|' . self::resolve($type->type),
            $type instanceof UnionType        => implode('|', array_map(self::resolve(...), $type->types)),
            $type instanceof IntersectionType => implode('&', array_map(self::resolve(...), $type->types)),
            $type instanceof Identifier,
            $type instanceof Name             => $type->toString(),
            default                           => (string) $type,
        };
    }
}