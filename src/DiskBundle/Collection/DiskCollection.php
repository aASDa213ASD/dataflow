<?php 

declare(strict_types=1);

namespace App\DiskBundle\Collection;

use App\DiskBundle\Entity\DiskDTO;
use Countable;
use ArrayIterator;
use IteratorAggregate;


class DiskCollection implements IteratorAggregate, Countable
{
	private array $disks = [];

	public function __construct(array $disks = [])
	{
		$this->disks = $disks;
	}

	public function add(DiskDTO $disk): void
	{
		$this->disks[] = $disk;
	}

	public function getOneByName(string $name): ?DiskDTO
	{
		foreach ($this->disks as $disk)
		{
			if ($disk->getName() === $name)
			{
				return $disk;
			}
		}
		
		return null;
	}

	public function count() : int
	{
		return count($this->disks);
	}

	public function getIterator() : ArrayIterator
	{
		return new ArrayIterator($this->disks);
	}
}

?>