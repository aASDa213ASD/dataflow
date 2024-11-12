<?php

declare(strict_types=1);

namespace App\FileBundle\Service;

use App\FileBundle\Entity\FileDTO;
use App\FileBundle\Entity\DirectoryDTO;
use App\AppBundle\Service\ConfigService;

class FileService
{
	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	/** Returns an array of files and folders */
	public function scanDirectory(string $path): DirectoryDTO
	{
		$directory = new DirectoryDTO();

		if (!is_dir($path))
		{
			return $directory;
		}

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
			$modificationTime = filemtime($file_path);

			// Create FileDTO object
			$fileDTO = new FileDTO($file_path, $item, $type, $size, $modificationTime);

			// Add to 'folders' or 'files' based on type
			if ($type === 'Folder')
			{
				$directory->addFolder($fileDTO);
			}
			else
			{
				$directory->addFile($fileDTO);
			}
		}

		return $directory;
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
