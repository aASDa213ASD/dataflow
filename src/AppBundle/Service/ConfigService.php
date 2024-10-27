<?php

declare(strict_types=1);

namespace App\AppBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigService
{
	private string $operatingSystem;

	public function __construct(ParameterBagInterface $params)
	{
		$this->operatingSystem = $params->get('app.operating_system');
	}

	public function getOperatingSystem(): string
	{
		return $this->operatingSystem;
	}
}
