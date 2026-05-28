<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Internal\FileGetContents;
use PhpParser\Node\Stmt\ClassMethod;
use PhpTree\Resolver\DocBlockResolver;
use function array_filter, array_values, array_map;

abstract class AbstractObjectNodeNormalizer
{
    public readonly string $name;
    public readonly string $fqcn;
    public readonly string $type;
    public readonly ?string $extends;
    public readonly array $extendsList;
    public readonly bool $isAbstract;
    public readonly string $filePath;
    public readonly bool $isFinal;
    public readonly array $implements;
    public readonly array $methods;
    public readonly ?string $description;

    public function __construct(
        public readonly Node $node, 
        public readonly string $namespace,
        public readonly FileGetContents $file,
        ...$args
    )
    {
        $this->name         = (string) $node->name;
        $this->fqcn         = $namespace
                                ? ($namespace. '\\' . $this->name)
                                : $this->name;
        $this->type         = static::TYPE;
        $this->extendsList  = $this->initExtendsList();
        $this->extends      = $this->initExtends();
        $this->isAbstract   = $this->initIsAbstract();
        $this->isFinal      = $this->initIsFinal();
        $this->filePath     = $file->fileName;
        $this->implements   = $this->initImplements();
        $this->methods      = $this->initMethods();
        $this->description  = DocBlockResolver::extractDescription($node);

        if (property_exists($this, "properties")) {
            $this->properties  = $this->initProperties();
        }

        if (property_exists($this, 'constants')) {
            $this->constants  = $this->initConstants();
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