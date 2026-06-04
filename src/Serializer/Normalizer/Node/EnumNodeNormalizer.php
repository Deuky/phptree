<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Trait\Constant;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Nop;
use PhpParser\Node\Stmt\EnumCase;
use function array_map, array_filter;

class EnumNodeNormalizer extends AbstractObjectNodeNormalizer
{
    use Constant;

    public readonly array $cases;

    public function __construct(...$args)
    {
        parent::__construct(...$args);

        $this->cases = $this->initCases();
    }

    protected function initImplements(): array
    {
        return array_map(
            fn(Name $n): string => $n->toString(),
            $this->node->implements,
        );
    }

    protected function initCases(): array
    {
        return array_map(
            fn(EnumCase $stmt) => new EnumCaseNodeNormalizer($stmt, $this->node),
            array_filter(
                $this->node->stmts,
                fn(Nop|EnumCase $stmt) => $stmt instanceof EnumCase 
            )
        );
    }
}