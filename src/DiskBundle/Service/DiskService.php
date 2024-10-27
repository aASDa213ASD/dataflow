<?php

declare(strict_types=1);

namespace App\DiskBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\Disk;

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
			/*
			$output = shell_exec("df -h --output=source,size,target | grep '^/dev/'");
			$lines = explode(PHP_EOL, trim($output));
			$disks = array_filter($lines, fn($line) => !empty(trim($line)));

			foreach ($disks as $line)
			{
				$disks[] = trim($line);
			}
			*/

			$output = shell_exec("df -h --output=source,size,used,avail,pcent,target | grep '^/dev'");
			$lines = explode(PHP_EOL, trim($output));
			$lines = array_filter($lines, fn($line) => !empty(trim($line)));

			foreach ($lines as $line)
			{
				[$filesystem, $size, $used, $avail, $usedPercentage, $mountPoint] = preg_split('/\s+/', trim($line));

				$disk = new Disk(
					$filesystem, $size,
					$mountPoint, $used,
					(float)rtrim($usedPercentage, '%')
				);

				$disks[] = $disk;
			}
		}
		else
		{
			foreach (range('A', 'Z') as $letter)
			{
				if (is_dir("$letter:\\"))
				{
					$disks[] = "$letter:\\";
				}
			}
		}

		return $disks;
	}
}
