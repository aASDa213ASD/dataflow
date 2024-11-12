<?php

declare(strict_types=1);

namespace App\FileBundle\Entity;

class DirectoryDTO
{
	/** @var FileDTO[] */
	private array $folders;

	/** @var FileDTO[] */
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

	public function addFolder(FileDTO $new_folder): self
	{
		$this->folders[] = $new_folder;
		return $this;
	}

	public function addFile(FileDTO $new_file): self
	{
		$this->files[] = $new_file;
		return $this;
	}
}

