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

	public function getFilesFromDisk(DiskDTO $disk, string $folder_path = ''): array
	{
		$mountpoint = $disk->getMountpoint() . DIRECTORY_SEPARATOR . trim($folder_path, '/');

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

			$type = 'Folder';

			if (!is_dir($path))
			{
				try
				{
					$type = ucfirst(pathinfo($item, PATHINFO_EXTENSION)); // Get file type from extension
				}
				catch (\Exception $e)
				{
					$type = 'Unknown';
				}
			}

			$size = filesize($path);
			$modificationTime = filemtime($path);

			$files[] = new FileDTO($path, $item, $type, $size, $modificationTime);
		}

		return $files;
	}
}
