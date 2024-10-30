<?php

declare(strict_types=1);

namespace App\FileBundle\Service;

use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Entity\DiskDTO;
use App\FileBundle\Entity\FileDTO;

class FileService
{
	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function getFilesFromDisk(DiskDTO $disk): array
	{
		$mountpoint = $disk->getMountpoint();

		if (!is_dir($mountpoint))
		{
			return [];
		}

		$files = [];
		$items = scandir($mountpoint);

		foreach ($items as $item)
		{
			$path = $mountpoint . DIRECTORY_SEPARATOR . $item;
			
			if ($item === '.' || $item === '..')
			{
				continue;
			}

			if (!is_readable($path))
			{
				continue;
			}

			$is_directory = is_dir($path);
			$size = $is_directory ? 0 : filesize($path);
			$modificationTime = filemtime($path);
			
			$files[] = new FileDTO($item, $path, $size, $modificationTime, $is_directory);
		}

		return $files;
	}
}
