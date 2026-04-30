<?php

namespace PhpTree\Serializer\Normalizer\Node;

class EnumNodeNormalizer extends AbstractNodeNormalizer
{
	const TYPE = "enum";

	protected function initExtends(): null
	{
		return null;
	}

	protected function initImplements(): array
	{
		return array_map(
            fn(Node\Name $n): string => $n->toString(),
            $this->node->implements,
        );
	}
}