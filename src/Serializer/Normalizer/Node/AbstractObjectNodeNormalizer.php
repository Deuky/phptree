<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Internal\FileGetContents;
use PhpParser\Node\Stmt\ClassMethod;
use function array_filter, array_values, array_map, property_exists;

abstract class AbstractObjectNodeNormalizer extends AbstractNodeNormalizer
{
    public readonly string $fqcn;
    public readonly ?string $extends;
    public readonly array $extendsList;
    public readonly bool $abstract;
    public readonly string $filePath;
    public readonly bool $final;
    public readonly array $implements;
    public readonly array $methods;

    public function __construct(
        Node $node, 
        public readonly string $namespace,
        public readonly FileGetContents $file,
        public readonly array $useClasses = [],
        public readonly array $useFunctions = [],
        ...$args
    )
    {
        parent::__construct($node);

        $this->fqcn         = $namespace
                                ? ($namespace. '\\' . $this->name)
                                : $this->name;
        $this->extendsList  = $this->initExtendsList();
        $this->extends      = $this->initExtends();
        $this->abstract     = $this->initIsAbstract();
        $this->final        = $this->initIsFinal();
        $this->filePath     = $file->fileName;
        $this->implements   = $this->initImplements();
        $this->methods      = $this->initMethods();

        foreach(
            [
                'properties' => $this->initProperties(...),
                'constants' => $this->initConstants(...),
            ] 
            as  $key => $callback
        ) {
            if (!property_exists($this, $key)) {
                continue;
            }

            $this->{$key} = $callback();
        }
    }

    protected function initExtendsList(): array
    {
        return [];
    }

    protected function initExtends(): string|null
    {
        return null;
    }

    protected function initIsAbstract(): bool
    {
        return false;
    }

    protected function initIsFinal(): bool
    {
        return false;
    }

    protected function initImplements(): array
    {
        return [];
    }

    protected function initConstants(): array
    {
        return [];
    }

    protected function initProperties(): array
    {
        return [];
    }

    protected function initMethods(): array
    {
        $stmts = $this->node->stmts ?? [];
 
        $methods = array_filter(
            $stmts,
            fn(Node $stmt): bool => $stmt instanceof ClassMethod,
        );

        return array_values(
            array_map(
                fn(ClassMethod $method): MethodNodeNormalizer => new MethodNodeNormalizer($method),
                $methods,
            )
        );
    }
}