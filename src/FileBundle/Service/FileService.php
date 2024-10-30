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

			$isDirectory = is_dir($path);
			$size = $isDirectory ? 0 : filesize($path);
			$modificationTime = filemtime($path);
			$extension = !$isDirectory ? pathinfo($item, PATHINFO_EXTENSION) : null;

			$type = 'directory';
			if (!$isDirectory) {
				$type = $extension ? 'file_with_extension' : 'file_without_extension';
			}

			$files[] = new FileDTO($item, $path, $size, $modificationTime, $type);
		}

		return $files;
	}
}
