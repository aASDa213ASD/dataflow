<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;


class FileDTO
{
	private string $name;
	private string $path;
	private int $size;
	private int $modification_time;
	private bool $is_directory;

	public function __construct(string $name, string $path, int $size, int $modification_time, bool $is_directory)
	{
		$this->name = $name;
		$this->path = $path;
		$this->size = $size;
		$this->modification_time = $modification_time;
		$this->is_directory = $is_directory;
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

	public function getSizeFormatted(): string
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
		return $this->modification_time;
	}

	public function getModificationTimeFormatted(): string
	{
		return date("d M Y - H:i A", $this->modification_time);
	}

	public function getIsDirectory(): bool
	{
		return $this->is_directory;
	}
}

