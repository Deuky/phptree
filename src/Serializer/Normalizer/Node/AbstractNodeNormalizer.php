<?php

namespace phpTree\Serializer\Normalizer\Node;

use PhpParser\Node;
use PhpTree\Internal\FileGetContents;

abstract class AbstractNodeNormalizer
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

	public function __construct(
		public readonly Node $node, 
		public readonly string $namespace,
		public readonly FileGetContents $file,
		...$args
	)
	{
		$this->name = (string) $node->name;
		$this->fqcn = $namespace
            ? ($namespace. '\\' . $this->name)
            : $this->name;
        $this->type = static::TYPE;
        $this->extendsList = $this->initExtendsList();
        $this->extends = $this->initExtends();
        $this->isAbstract = $this->initIsAbstract();
        $this->isFinal = $this->initIsFinal();
        $this->filePath = $file->fileName;
        $this->implements = $this->initImplements();
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
}