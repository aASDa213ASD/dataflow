<?php

declare(strict_types=1);

namespace App\FileBundle\Service;

use App\FileBundle\Entity\FileDto;
use App\FileBundle\Entity\DirectoryDto;
use App\AppBundle\Service\ConfigService;

class FileService
{
	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function scanDirectory(string $path): DirectoryDto
	{
		if (!is_dir($path))
		{
			return new DirectoryDto();
		}

		$folders = [];
		$files = [];
		$items = scandir($path);

		foreach ($items as $item)
		{
			$file_path = $path . DIRECTORY_SEPARATOR . $item;

			// Skip special directory entries
			if ($item === '.' || $item === '..')
			{
				continue;
			}

			// Ensure we have permissions to read the path
			if (!is_readable($file_path))
			{
				continue;
			}

			$type = is_dir($file_path) ? 'Folder' : ucfirst(pathinfo($item, PATHINFO_EXTENSION));
			$size = filesize($file_path);
			$creation_time = filectime($file_path);
			$modification_time = filemtime($file_path);

			$item = new FileDto($file_path, $item, $type, $size, $creation_time, $modification_time);

			// Add to 'folders' or 'files' based on type
			if ($type === 'Folder')
			{
				$folders[] = $item;
			}
			else
			{
				$files[] = $item;
			}
		}

		return new DirectoryDto($folders, $files);
	}

	public function getFile(string $path): ?FileDto
	{
		if (!is_readable($path))
		{
			return null;
		}

		$name = basename($path);
		$type = is_dir($path) ? 'Folder' : ucfirst(pathinfo($path, PATHINFO_EXTENSION));
		$size = filesize($path);
		$modification_time = filemtime($path);

		return new FileDto($path, $name, $type, $size, $modification_time);
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
