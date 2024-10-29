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

		if ($this->config->getOperatingSystem() === 'linux')
		{
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
		}
		elseif ($this->config->getOperatingSystem() === 'windows')
		{
			// Windows-specific command using WMIC
			$output = shell_exec('wmic logicaldisk get Caption,Size,FreeSpace');
			$lines = explode(PHP_EOL, trim($output));
			$lines = array_filter($lines, fn($line) => !empty(trim($line)) && strpos($line, 'Caption') === false);

			foreach ($lines as $line)
			{
				$columns = preg_split('/\s+/', trim($line));
				if (count($columns) < 3)
				{
					continue;
				}

				[$filesystem, $freeSpace, $totalSpace] = $columns;

				$size = (int)$totalSpace;
				$avail = (int)$freeSpace;
				$used = $size - $avail;
				$usedPercentage = $size > 0 ? ($used / $size) * 100 : 0;

				$disk = new DiskDTO(
					$filesystem,
					$this->formatSize($size),
					$filesystem,
					$this->formatSize($used),
					round($usedPercentage, 2)
				);

				$disks[] = $disk;
			}
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
