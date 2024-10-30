<?php

declare(strict_types=1);

namespace App\DiskBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\DiskDTO;
use RuntimeException;

class DiskService
{
	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function getAvailableDisks(): array
	{
		$disks = [];

		switch ($this->config->getOperatingSystem())
		{
			case 'linux':
				$disks = $this->getLinuxDisks();
				break;
			case 'windows':
				$disks = $this->getWindowsDisks();
				break;
			default:
				throw new RuntimeException("Unsupported operating system.");
		}

		return $disks;
	}

	private function getLinuxDisks(): array
	{
		$disks = [];
		$output = shell_exec("df -h --output=source,size,used,avail,pcent,target | grep '^/dev'");
		$lines = array_filter(explode(PHP_EOL, trim($output)), fn($line) => !empty(trim($line)));

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

	private function getWindowsDisks(): array
	{
		$disks = [];
		$output = shell_exec('fsutil fsinfo drives');
		$drive_letters = explode(' ', trim(str_replace('Drives: ', '', $output)));

		foreach ($drive_letters as $drive)
		{
			$diskInfo = shell_exec("fsutil volume diskfree {$drive}");

			if (!$diskInfo)
			{
				continue;
			}

			preg_match_all('/\d+/', $diskInfo, $matches);
			if (count($matches[0]) < 2)
			{
				continue;
			}

			$total_bytes = (int)$matches[0][1];
			$free_bytes = (int)$matches[0][0];
			$used_bytes = $total_bytes - $free_bytes;
			$used_percentage = $total_bytes > 0 ? ($used_bytes / $total_bytes) * 100 : 0;

			$disk = new DiskDTO(
				$drive, $this->bytesToReadableSize($total_bytes),
				$drive, $this->bytesToReadableSize($used_bytes),
				round($used_percentage, 2)
			);

			$disks[] = $disk;
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

	private function bytesToReadableSize(int $bytes): string
	{
		$units = ['B', 'KB', 'M', 'G', 'T'];
		$unitIndex = 0;

		while ($bytes >= 1024 && $unitIndex < count($units) - 1)
		{
			$bytes /= 1024;
			$unitIndex++;
		}

		return round($bytes, 2) . ' ' . $units[$unitIndex];
	}
}
