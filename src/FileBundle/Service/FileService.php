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

	public function getDirectory(string $path): DirectoryDto
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

			if ($item === '.' || $item === '..')
			{
				continue;
			}

			if (!is_readable($file_path))
			{
				continue;
			}

			$type = is_dir($file_path) ? 'Folder' : ucfirst(pathinfo($item, PATHINFO_EXTENSION));
			$size = filesize($file_path);
			$creation_time = filectime($file_path);
			$modification_time = filemtime($file_path);

			$item = new FileDto($file_path, $item, $type, $size, $creation_time, $modification_time);

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

	public function rename(string $path, string $new_name): bool
	{
		$directory = dirname($path);
		$new_path  = $directory . DIRECTORY_SEPARATOR . $new_name;

		return rename($path, $new_path);
	}

	public function move(string $path, string $destination): bool
	{
		return rename($path, $destination);
	}

	public function copy(string $path, string $destination): bool
	{
		return copy($path, $destination);
	}

	public function delete(string $path): bool
	{
		return unlink($path);
	}

	public function getHistory(string $path): array
	{
		$parts = explode(DIRECTORY_SEPARATOR, $path);
		$accumulated_path = array_shift($parts) . DIRECTORY_SEPARATOR;
		$history[] = [
			'path' => $accumulated_path,
			'name' => $accumulated_path
		];

		foreach ($parts as $part)
		{
			if ($part == '')
			{
				continue;
			}

			$accumulated_path .= $part . DIRECTORY_SEPARATOR;

			$history[] = [
				'path' => rtrim($accumulated_path, DIRECTORY_SEPARATOR),
				'name' => $part
			];
		}
	
		return $history;
	}
}
