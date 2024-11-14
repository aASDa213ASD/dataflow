<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\AppBundle\Entity\ColorThemeDto;
use App\AppBundle\Entity\ConfigurationDto;
use App\AppBundle\Service\ConfigService;
use App\DiskBundle\Service\DiskService;
use App\FileBundle\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommonController extends AbstractController
{
	private ConfigService $config_service;
	private DiskService $disk_service;
	private FileService $file_service;

	public function __construct(ConfigService $config_service, DiskService $disk_service, FileService $file_service)
	{
		$this->config_service = $config_service;
		$this->disk_service = $disk_service;
		$this->file_service = $file_service;
	}

	#[Route('/', name: 'root')]
	public function root(): Response
	{
		$disks = $this->disk_service->getAvailableDisks();
		$config = $this->config_service->get();

		return $this->render('@UserInterface/root.html.twig', [
			'disks' => $disks,
			'config' => $config,
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
	public function getFilesTable(Request $request): Response
	{
		$path = $request->query->get('path');
		$directory = $this->file_service->scanDirectory($path);

		return $this->render('@UserInterface/_files_table.html.twig', [
			'directory' => $directory,
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

	#[Route('/settings', name: 'settings')]
	public function settings(Request $request): Response
	{
		if ($request->isMethod('POST'))
		{
			$payload = json_decode($request->getContent(), true);

			if (!empty($payload))
			{
				$config = new ConfigurationDto(
					$payload['operating_system'],
					new ColorThemeDto(...$payload['color_theme']),
				);
			}
			else
			{
				$config = new ConfigurationDto();
			}

			$this->config_service->save($config);
		}

		$config = $this->config_service->get();

		return $this->render('@UserInterface/_settings.html.twig', [
			'config' => $config,
		]);
	}

	#[Route('/file/properties/', name: 'file_properties')]
	public function getFileProperties(Request $request): Response
	{
		$path = $request->query->get('path');
		$file = $this->file_service->getFile($path);

		return $this->render('@UserInterface/modal_properties.html.twig', [
			'file' => $file
		]);
	}
}
