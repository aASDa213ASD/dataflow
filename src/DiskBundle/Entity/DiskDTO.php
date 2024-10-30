<?php

declare(strict_types=1);

namespace App\DiskBundle\Entity;


class DiskDTO
{
	private string $name;
	private string $mountpoint;
	private int $size;
	private int $used;
	private float $used_percentage;

	public function __construct(string $name, string $mountpoint, int $size, int $used, float $used_percentage)
	{
		$this->name = $name;
		$this->size = $size;
		$this->mountpoint = $mountpoint;
		$this->used = $used;
		$this->used_percentage = $used_percentage;
	}

	public function getName(bool $readable = false): string
	{
		if ($readable)
		{
			return preg_replace('/^\/dev\//', '', $this->name);
		}

		return $this->name;
	}

	public function getMountpoint(): string
	{
		return $this->mountpoint;
	}

	public function getSize(): int
	{
		return $this->size;
	}

	public function getUsed(): int
	{
		return $this->used;
	}

	public function getUsedPercentage(): string
	{
		return $this->used_percentage . '%';
	}
}
