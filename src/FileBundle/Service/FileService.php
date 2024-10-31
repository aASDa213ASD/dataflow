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

	public function getFilesFromDisk(string $path): array
	{
		if (!is_dir($path))
		{
			return [];
		}

		$files = [];
		$items = scandir($path);

		foreach ($items as $item)
		{
			$file_path = $path . DIRECTORY_SEPARATOR . $item;

			if ($item === '.' || $item === '..')
			{
				continue;
			}

			if (!is_readable($file_path))
			{
				continue;
			}

			$type = 'Folder';

			if (!is_dir($file_path))
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

			$size = filesize($file_path);
			$modificationTime = filemtime($file_path);

			$files[] = new FileDTO($file_path, $item, $type, $size, $modificationTime);
		}

		return $files;
	}

	public function generatePathHistory(string $path): array
	{
		$parts = explode(DIRECTORY_SEPARATOR, $path);
		$accumulatedPath = array_shift($parts) . DIRECTORY_SEPARATOR;
		$history[] = [
			'path' => $accumulatedPath,
			'name' => $accumulatedPath
		];

		foreach ($parts as $part)
		{
			if ($part == '')
			{
				continue;
			}
			
			$accumulatedPath .= $part . DIRECTORY_SEPARATOR;
			$history[] = [
				'path' => rtrim($accumulatedPath, DIRECTORY_SEPARATOR),
				'name' => $part
			];
		}
	
		return $history;
	}
}
