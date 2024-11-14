<?php

declare(strict_types=1);

namespace App\DiskBundle\Entity;

class DiskDto
{
	public function __construct(
		private readonly string $name,
		private readonly string $mountpoint,
		private readonly int $size,
		private readonly int $used,
		private readonly float $used_percentage
	)
	{

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
