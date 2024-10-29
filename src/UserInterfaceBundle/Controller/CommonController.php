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

	#[Route('/disks', name: 'disks')]
	public function viewDisks(): Response
	{
		$disks = $this->diskService->getAvailableDisks();

		return $this->render('@UserInterface/disks_view.html.twig', [
			'disks' => $disks
		]);
	}

	#[Route('/files', name: 'files')]
	public function viewFiles(): Response
	{
		$disks = $this->diskService->getAvailableDisks();
		$files = $this->fileService->getFilesFromDisk($disks[0]);

		return $this->render('@UserInterface/disks_view.html.twig', [
			'disks' => $disks,
			'files' => $files
		]);
	}
}
