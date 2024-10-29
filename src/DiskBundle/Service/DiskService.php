<?php

declare(strict_types=1);

namespace App\DiskBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\DiskDTO;

class DiskService
{
	private ConfigService $configService;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function getAvailableDisks(): array
	{
		$disks = [];


		$output = shell_exec("df -h --output=source,size,used,avail,pcent,target | grep '^/dev'");
		$lines = explode(PHP_EOL, trim($output));
		$lines = array_filter($lines, fn($line) => !empty(trim($line)));

		foreach ($lines as $line)
		{
			[$filesystem, $size, $used, $avail, $usedPercentage, $mountPoint] = preg_split('/\s+/', trim($line));

			$disk = new DiskDTO(
				$filesystem, $size,
				$mountPoint, $used,
				(float)rtrim($usedPercentage, '%')
			);

			$disks[] = $disk;
		}

		return $disks;
	}

	public function getDiskByName(string $name): ?DiskDTO
	{
		$disks = $this->getAvailableDisks();

		foreach ($disks as $disk)
		{
			if ($disk->getName() === $name)
			{
				return $disk;
			}
		}

		return null;
	}

	public function getDiskByMountPoint(string $mount_point): ?DiskDTO
	{
		$disks = $this->getAvailableDisks();

		foreach ($disks as $disk)
		{
			if ($disk->getMountpoint() === $mount_point)
			{
				return $disk;
			}
		}

		return null;
	}
}
