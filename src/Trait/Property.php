<?php

namespace PhpTree\Trait;

use PhpParser\Node;
use PhpParser\Serializer\Normalizer\ConstantNodeNormalizer;
use PhpParser\Node\Stmt\Property as StmtProperty;
use PhpTree\Serializer\Normalizer\Node\PropertyNodeNormalizer;

use function array_filter, array_values;

trait Property
{
    use Constructor;

    public readonly array $properties;

	protected function initProperties(): array
    {
        $stmts = $this->node->stmts ?? [];

        $propertyNodes = array_filter(
            $stmts,
            fn(Node $stmt): bool => $stmt instanceof StmtProperty,
        );

        $properties = [];
        foreach ($propertyNodes as $property) {
            foreach ($property->props as $prop) {
                $properties[(string) $prop->name] = new PropertyNodeNormalizer(
                                                            $property,
                                                            $prop,
                                                        );
            }
        }

        $this->initConstructor();

        $properties = array_reduce(
            $this->getConstructorProperties(),
            function($carry, $item) {
                $name = trim($item->name, '$');
                if (!array_key_exists($name, $carry)) {
                    $carry[$name] = $item;
                }
                return $carry;
            },
            $properties
        );

        return array_values($properties);
    }
}