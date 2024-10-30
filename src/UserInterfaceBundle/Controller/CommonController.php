<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\DiskBundle\Service\DiskService;
use App\FileBundle\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

	#[Route('/{disk_name}{path}', name: 'files', requirements: ['path' => '.+'])]
	public function files(Request $request, string $disk_name, string $path): Response
	{
		$disks = $this->disk_service->getAvailableDisks(); // TODO: Get disk by name from $disks variable instead
		$disk = $this->disk_service->getDiskByName($disk_name);

		$files = $this->file_service->getFilesFromDisk($path);

		return $this->render('@UserInterface/files_view.html.twig', [
			'disks' => $disks,
			'disk' => $disk,
			'files' => $files,
			'path' => $path,
		]);
	}

	#[Route('/settings', name: 'settings', priority: 1)]
	public function settings(): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		return $this->render('@UserInterface/settings.html.twig', [
			'disks' => $disks,
		]);
	}
}
