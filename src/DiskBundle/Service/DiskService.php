<?php

declare(strict_types=1);

namespace App\DiskBundle\Service;

use App\AppBundle\Helper\BytesReader;
use App\AppBundle\Service\ConfigService;
use App\AppBundle\Entity\ConfigurationDto;
use App\DiskBundle\Entity\DiskCollection;
use App\DiskBundle\Entity\DiskDto;
use RuntimeException;

class DiskService
{
	private ConfigurationDto $config;

	public function __construct(ConfigService $config_service)
	{
		$this->config = $config_service->get();
	}

	public function getAvailableDisks(): DiskCollection
	{
		$disks = match ($this->config->getOperatingSystem())
		{
			'Linux' => $this->getLinuxDisks(),
			'Windows' => $this->getWindowsDisks(),
			default => throw new RuntimeException("Unsupported operating system."),
		};

		return new DiskCollection($disks);
	}

	private function getLinuxDisks(): array
	{
		$disks = [];
		$output = shell_exec("df -h --block-size=1 --output=source,size,used,avail,pcent,target | grep '^/dev'");
		$lines = array_filter(explode(PHP_EOL, trim($output)), fn($line) => !empty(trim($line)));

		foreach ($lines as $line)
		{
			[$filesystem, $size, $used, $avail, $used_percentage, $mountpoint] = preg_split('/\s+/', trim($line));

			$disk = new DiskDto(
				$filesystem, $mountpoint,
				(int)$size, (int)$used,
				(float)rtrim($used_percentage, '%')
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

			preg_match_all('/([\d,]+)\s?\ \(/', $diskInfo, $matches);

			$free_bytes = (int)str_replace(',', '', $matches[0][0]);
			$total_bytes = (int)str_replace(',', '', $matches[0][1]);
			$used_bytes = $total_bytes - $free_bytes;
			$used_percentage = $total_bytes > 0 ? ($used_bytes / $total_bytes) * 100 : 0;

			$disk = new DiskDto(
				$drive, $drive,
				$total_bytes, $used_bytes,
				round($used_percentage)
			);

			$disks[] = $disk;
		}

		return $disks;
	}
}
