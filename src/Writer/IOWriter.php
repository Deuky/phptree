<?php

namespace PhpTree\Writer;

use PhpTree\Interface\WriterInterface;

class IOWriter implements WriterInterface
{
	protected readonly mixed $fopen;

	public function __construct(mixed $output)
	{
		if (is_string($output)) {
			$this->fopen = fopen($output, "w+");
		}

		if (is_resource($output)) {
			$this->fopen = $output;
		}


		$this->fopen ?? null ?: throw new \Exception();
	}

	public function write(string $content): void
	{
		fwrite($this->fopen, $content);
	}
}