<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\DiskBundle\Service\DiskService;
use App\FileBundle\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
	private DiskService $diskService;
	private FileService $fileService;

	public function __construct(DiskService $diskService, FileService $fileService)
	{
		$this->diskService = $diskService;
		$this->fileService = $fileService;
	}

	#[Route('/', name: 'disk_selection')]
	public function disks(): Response
	{
		$disks = $this->diskService->getAvailableDisks();

		return $this->render('@UserInterface/disk_selection.html.twig', [
			'disks' => $disks,
		]);
	}

	#[Route('/{disk_name}', name: 'files')]
	public function files(string $disk_name): Response
	{
		$disks = $this->diskService->getAvailableDisks();
		$disk = $this->diskService->getDiskByName($disk_name);
		
		if ($disk)
		{
			$files = $this->fileService->getFilesFromDisk($disk);

			return $this->render('@UserInterface/files_view.html.twig', [
				'disks' => $disks,
				'files' => $files,
			]);
		}

		return $this->redirectToRoute('disk_selection');
	}
}
