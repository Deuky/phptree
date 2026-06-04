<?php

namespace PhpTree\Resolver;

use PhpParser\Node;
use PhpParser\Node\ComplexType;
use PhpParser\Node\NullableType;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\UnionType;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\EnumCase;

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
            $type instanceof ClassMethod      => self::resolve($type->returnType),
            $type instanceof Param,
            $type instanceof ClassConst,
            $type instanceof Property         => self::resolve($type->type),
            //$type instanceof EnumCase         => self::resolve($type->type),
            $type instanceof Class_           => 'class',
            $type instanceof Enum_            => 'enum',
            $type instanceof Trait_           => 'trait',
            $type instanceof Interface_       => 'interface',
            default                           => (function() {
                                                        echo "resolv";
                                                        print_r(
                                                            func_get_args()
                                                        );
                                                            die();

                                                    })($type),
            //default                           => (string) $type,
        };
    }
}