<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;


class FileDTO
{
	private string $name;
	private string $path;
	private int $size;
	private int $modificationTime;

	public function __construct(string $name, string $path, int $size, int $modificationTime)
	{
		$this->name = $name;
		$this->path = $path;
		$this->size = $size;
		$this->modificationTime = $modificationTime;
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

	public function getFormattedSize(): string
	{
		if ($this->size < 1024)
		{
			return $this->size . ' B';
		}
		elseif ($this->size < 1048576)
		{
			return round($this->size / 1024, 2) . ' KB';
		}
		else
		{
			return round($this->size / 1048576, 2) . ' MB';
		}
	}

	public function getModificationTime(): int
	{
		return $this->modificationTime;
	}

	public function getFormattedModificationTime(): string
	{
		return date("d M Y - H:i A", $this->modificationTime);
	}
}
