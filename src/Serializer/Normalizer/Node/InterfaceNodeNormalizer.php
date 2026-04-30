<?php

namespace PhpTree\Serializer\Normalizer\Node;

use PhpParser\Node;

class InterfaceNodeNormalizer extends AbstractNodeNormalizer
{
	const TYPE = "interface";

	protected function initExtends(): string
	{
        return implode(', ', $this->extendsList);
	}
}