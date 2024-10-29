<?php

declare(strict_types=1);

namespace App\DiskBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\DiskDTO;

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
			$output = shell_exec('fsutil fsinfo drives'); // fsutil fsinfo ntfsInfo C:
			$output = str_replace('Drives: ', '', $output);
			$lines = explode(' ', trim($output));

			foreach ($lines as $line)
			{
				$disk_info = shell_exec("fsutil fsinfo ntfsInfo {$line}");
				$disk_info = str_replace(' ', '', $disk_info);
				$columns = explode("\n", trim($disk_info));
				
				$relative_data = [];
				$relative_data[] = $columns[4]; // total sectors 
				$relative_data[] = $columns[5]; // free clusters

				foreach ($relative_data as $index => $data)
				{
					if (preg_match('/([\d,]+)\s?\(/', $data, matches: $matches))
					{
						$value = (int) str_replace(',', '', $matches[1]);
						$relative_data[$index] = $value;
					}
				}
				
				$size = $relative_data[0];
				$used = $relative_data[0] - $relative_data[1];
				$usedPercentage = $size > 0 ? ($used / $size) * 100 : 0;
				
				// Now call formatSize with integer arguments
				$disk = new DiskDTO(
					$line,
					$this->bytesToReadableSize($size),
					$line,
					$this->bytesToReadableSize($used),
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

	private function bytesToReadableSize(int $bytes): string
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
