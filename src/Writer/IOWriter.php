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

	public function write(string|array ...$contents): void
	{
		array_map($this->writeln(...), $contents);
	}

	public function writeln(string|array $line): void
	{
		if (is_array($line)) {
			$this->write(...$line);
			return;
		}
		fwrite($this->fopen, $line.PHP_EOL);
	}

	public function __destruct()
	{
		fclose($this->fopen);
	}
}