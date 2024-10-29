<?php

declare(strict_types=1);

namespace App\DiskBundle\Entity;


class DiskDTO
{
	private string $name;
	private string $size;
	private string $mountpoint;
	private string $used;
	private float $usedPercentage;

	public function __construct(string $name, string $size, string $mountpoint, string $used, float $usedPercentage)
	{
		$this->name = $name;
		$this->size = $size;
		$this->mountpoint = $mountpoint;
		$this->used = $used;
		$this->usedPercentage = $usedPercentage;
	}

	public function getName(): string
	{
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
		return number_format($this->usedPercentage, 2) . '%';
	}
}
