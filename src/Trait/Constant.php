<?php

namespace PhpTree\Trait;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassConst as StmtConst;
use PhpTree\Serializer\Normalizer\Node\ConstantNodeNormalizer;

trait Constant
{
    public readonly array $constants;

	protected function initConstants(): array
    {
        $stmts = $this->node->stmts ?? [];
        $constNodes = array_filter(
            $stmts,
            fn(Node $stmt): bool => $stmt instanceof StmtConst,
        );

        $constants = [];
        foreach ($constNodes as $classConst) {
            foreach ($classConst->consts as $const) {
                $constants[] = new ConstantNodeNormalizer($classConst, $const);
            }
        }

        return array_values($constants);
    }
}