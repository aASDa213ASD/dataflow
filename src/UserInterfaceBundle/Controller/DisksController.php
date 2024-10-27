<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Controller;

use App\DiskBundle\Service\DiskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DisksController extends AbstractController
{
	private DiskService $diskService;

	public function __construct(DiskService $diskService)
	{
		$this->diskService = $diskService;
	}

	#[Route('/disks', name: 'disks')]
	public function viewDisks(): Response
	{
		$disks = $this->diskService->getAvailableDisks();

		return $this->render('@UserInterface/disks_view.html.twig', [
			'disks' => $disks
		]);
	}
}
