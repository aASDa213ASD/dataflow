<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\DiskBundle\Service\DiskService;
use App\FileBundle\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

	#[Route('/', name: 'root')]
	public function root(): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		return $this->render('@UserInterface/root.html.twig', [
			'disks' => $disks,
		]);
	}

	#[Route('/disks', name: 'disks')]
	public function getDisksSelector(Request $request): Response
	{
		$disks = $this->disk_service->getAvailableDisks();

		return $this->render('@UserInterface/_disks_selection.html.twig', [
			'disks' => $disks,
		]);
	}

	#[Route('/dev', name: 'files')]
	public function getFilesTable(Request $request)
	{
		$path = $request->query->get('path');
		$files = $this->file_service->getFiles($path);

		return $this->render('@UserInterface/_files_table.html.twig', [
			'files' => $files,
			'path'  => $path,
		]);
	}

	#[Route('/history', name:'history')]
	public function getFileHistoryBreadcrumb(Request $request): Response
	{
		$path = $request->query->get('path');
		$history = $this->file_service->generatePathHistory($path);

		return $this->render('@UserInterface/_history.html.twig', [
			'history' => $history,
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
