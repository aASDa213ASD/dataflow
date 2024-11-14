<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;

class FileDto
{
	public function __construct(
		private readonly string $path,
		private readonly string $name,
		private readonly string $type,
		private readonly int $size,
		private readonly int $modification_time
	)
	{

	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getPath(): string
	{
		return $this->path;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function getModificationTime(): int
	{
		return $this->modification_time;
	}

	public function getModificationTimeFormatted(): string
	{
		return date("d M Y", $this->modification_time) . ' at ' . date("H:i A", $this->modification_time);
	}

	public function getType(): string
	{
		return $this->type;
	}
}

