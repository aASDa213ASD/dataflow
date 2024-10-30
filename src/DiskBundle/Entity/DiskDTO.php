<?php

declare(strict_types=1);

namespace App\DiskBundle\Entity;


class DiskDTO
{
	private string $name;
	private string $size;
	private string $mountpoint;
	private string $used;
	private float $used_percentage;

	public function __construct(string $name, string $size, string $mountpoint, string $used, float $used_percentage)
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

	public function getSize(): string
	{
		return $this->size;
	}

	public function getMountpoint(): string
	{
		return $this->mountpoint;
	}

	public function getUsed(): string
	{
		return $this->used;
	}

	public function getUsedPercentage(): string
	{
		return $this->used_percentage . '%';
	}
}
