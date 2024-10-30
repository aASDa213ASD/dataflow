<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\DiskBundle\Service\DiskService;
use App\FileBundle\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
	private DiskService $disk_service;
	private FileService $file_service;

	public function __construct(DiskService $disk_service, FileService $file_service)
	{
		$this->disk_service = $disk_service;
		$this->file_service = $file_service;
	}

	#[Route('/', name: 'disk_selection')]
	public function disks(): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		return $this->render('@UserInterface/disk_selection.html.twig', [
			'disks' => $disks,
		]);
	}

	#[Route('/{disk_name}', name: 'files')]
	public function files(string $disk_name): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		$disk = $this->disk_service->getDiskByName($disk_name);

		if (!$disk)
		{
			return $this->redirectToRoute('disk_selection');
		}

		$files = $this->file_service->getFilesFromDisk($disk);

		return $this->render('@UserInterface/files_view.html.twig', [
			'disks' => $disks,
			'files' => $files,
		]);
	}

	#[Route('/dataflow/settings', name: 'settings')]
	public function settings(): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		return $this->render('@UserInterface/settings.html.twig', [
			'disks' => $disks,
		]);
	}
}
