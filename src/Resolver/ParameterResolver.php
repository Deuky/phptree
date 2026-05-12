<?php

namespace PhpTree\Resolver;

use PhpParser\Node\Param;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;
use PhpParser\Node\NullableType;

class ParameterResolver
{
    public static function resolve($default): ?string
    {
        if ($default === null) {
            return null;
        }

        return match (true) {
            $default instanceof Expr\ConstFetch      => (string) $default->name,
            $default instanceof Scalar\Int_,
            $default instanceof Scalar\Float_        => (string) $default->value,
            $default instanceof Scalar\String_       => sprintf("'%s'", $default->value),
            $default instanceof Expr\Array_          => '[]',
            $default instanceof Expr\UnaryMinus      => '-' . $default->expr->value,
            default                                  => 'unknown',
        };
    }
}