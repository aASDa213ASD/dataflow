<?php

declare(strict_types=1);

namespace App\FileBundle\Service;

use App\DiskBundle\Entity\DiskDTO;
use App\FileBundle\Entity\FileDTO;
use Symfony\Component\Finder\Finder;
use App\AppBundle\Service\ConfigService;

class FileService
{
	private ConfigService $config;

	public function __construct(ConfigService $config)
	{
		$this->config = $config;
	}

	public function getFiles(string $path): array
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

			if (!is_dir($file_path))
			{
				$type = ucfirst(pathinfo($item, PATHINFO_EXTENSION));
			}
			else
			{
				$type = 'Folder';
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

	public function getSortedFiles(string $path, string $order = 'asc'): array
    {
        $finder = new Finder();
        $finder->in($path)->depth('== 0')->sortByName();
		$finder->notName(['*.sys', '*.tmp']);

        if ($order === 'desc') 
		{
            $finder->reverseSorting();
        }

        $files = [];
		foreach ($finder as $file)
		{
			$file_path = $file->getRealPath() ?: $file->getPathname();
			$item = $file->getFilename();
			$type = $file->isDir() ? 'Folder' : 'File';
			$size = $file->getSize();
			$modificationTime = $file->getMTime();

			$files[] = new FileDTO($file_path, $item, $type, $size, $modificationTime);
		}

        return $files;
    }
}
