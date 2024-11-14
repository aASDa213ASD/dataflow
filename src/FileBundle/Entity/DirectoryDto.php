<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;

readonly class DirectoryDto
{
	/** @var FileDto[] */
	private array $folders;

	/** @var FileDto[] */
	private array $files;

	public function __construct(array $folders = [], array $files = [])
	{
		$this->folders = $folders;
		$this->files = $files;
	}

	public function getFolders(): array
	{
		return $this->folders;
	}

	public function getFiles(): array
	{
		return $this->files;
	}
}

