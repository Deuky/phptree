<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node\Stmt\ClassMethod;

class MethodNodeNormalizer
{
    public readonly string $name;
    public readonly string $visibility;
    public readonly bool $isStatic;
    public readonly bool $isAbstract;
    public readonly TypeNodeNormalizer $returnType;
    public readonly array $parameters;

    public function __construct(public readonly ClassMethod $node)
    {
        $this->name       = (string) $node->name;
        $this->visibility = $this->initVisibility();
        $this->isStatic   = $node->isStatic();
        $this->isAbstract = $node->isAbstract();
        $this->returnType = new TypeNodeNormalizer($node->returnType);
        $this->parameters = $this->initParameters();
    }

    private function initVisibility(): string
    {
        return match (true) {
            $this->node->isPublic()    => 'public',
            $this->node->isProtected() => 'protected',
            default                    => 'private',
        };
    }

    private function initParameters(): array
    {
        return array_map(
            fn(\PhpParser\Node\Param $param): ParameterNodeNormalizer => new ParameterNodeNormalizer($param),
            $this->node->params,
        );
    }
}