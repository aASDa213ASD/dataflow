<?php

declare(strict_types=1);

namespace App\FileBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\DiskDTO;
use App\DiskBundle\Entity\FileDTO;

class FileService
{
	private ConfigService $configService;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function getFilesFromDisk(DiskDTO $disk): array
	{
		$mount_point = $disk->getMountpoint();

		if (!is_dir($mount_point)) {
			return [];
		}

		return array_diff(scandir($mount_point), ['.', '..']);
	}
}
