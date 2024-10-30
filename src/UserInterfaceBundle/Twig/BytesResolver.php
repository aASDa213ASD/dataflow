<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BytesResolver extends AbstractExtension
{
	public function getFunctions(): array
	{
		return [
			new TwigFunction('bytesToReadableSize', [$this, 'bytesToReadableSize']),
		];
	}

	public function bytesToReadableSize(int $bytes): string
	{
		$units = ['B', 'KB', 'MB', 'GB', 'TB'];
		$unitIndex = 0;

		while ($bytes >= 1024 && $unitIndex < count($units) - 1)
		{
			$bytes /= 1024;
			$unitIndex++;
		}

		return round($bytes, 0) . ' ' . $units[$unitIndex];
	}
}