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
				$disk_info = shell_exec("fsutil volume diskfree {$line}");
				$disk_info = str_replace(' ', '', $disk_info);
				$disk_info = str_replace('AlocalNTFSvolumeisrequiredforthisoperation.', '', $disk_info);
				$columns = explode("\n", trim($disk_info));
			
				$data = [];
				$data['free_bytes'] = $columns[0];  // free bytes
				$data['total_bytes'] = $columns[1]; // total bytes 

				foreach ($data as $key => $value)
				{
					if (preg_match('/([\d,]+)\s?\(/', $value, matches: $matches))
					{
						$bytes = (int) str_replace(',', '', $matches[1]);
						$data[$key] = $bytes;
					}
				}
				
				$used = $data['total_bytes'] - $data['free_bytes'];
				$used_percentage = $data['total_bytes'] > 0 ? ($used / $data['total_bytes']) * 100 : 0;
				
				$disk = new DiskDTO(
					$line,
					$this->bytesToReadableSize($data['total_bytes']),
					$line,
					$this->bytesToReadableSize($used),
					round($used_percentage, 2)
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
