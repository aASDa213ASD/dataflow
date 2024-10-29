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
			$output = shell_exec('powershell -command "Get-PSDrive -PSProvider FileSystem | Select-Object Root, Used, Free"');
			$lines = explode("\n", trim($output));
			$lines = array_slice($lines, 2);

			foreach ($lines as $line)
			{
				$columns = preg_split('/\s+/', trim($line));
				if (count($columns) < 3)
				{
					continue;
				}

				[$filesystem, $usedSpace, $freeSpace] = $columns;

				// Convert string values to integers
				$avail = (int)$freeSpace;
				$used = (int)$usedSpace;
				$size = $used + $avail;
				$usedPercentage = $size > 0 ? ($used / $size) * 100 : 0;

				// Now call formatSize with integer arguments
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

		if ($this->config->getOperatingSystem() === 'linux')
		{
			$name = "/dev/{$name}";
		}

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

	private function formatSize(int $bytes): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		$unitIndex = 0;

		while ($bytes >= 1024 && $unitIndex < count($units) - 1)
		{
			$bytes /= 1024;
			$unitIndex++;
		}

		return round($bytes, 2) . ' ' . $units[$unitIndex];
	}
}
