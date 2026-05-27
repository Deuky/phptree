<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpTree\Trait\Constant;

class EnumNodeNormalizer extends AbstractObjectNodeNormalizer
{
	use Constant;

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