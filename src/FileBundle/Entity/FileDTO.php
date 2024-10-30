<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;


class FileDTO
{
	private string $path;
	private string $name;
	private string $type;
	private int $size;
	private int $modification_time;

	public function __construct(string $path, string $name, string $type, int $size, int $modification_time)
	{
		$this->path = $path;
		$this->name = $name;
		$this->type = $type;
		$this->size = $size;
		$this->modification_time = $modification_time;
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
		return date("d M Y", $this->modification_time) . ' at ' . date("H:i A", $this->modification_time); ;
	}

	public function getType(): string
	{
		return $this->type;
	}
}

