<?php

declare(strict_types=1);

namespace App\AppBundle\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ConfigService
{
	private string $operating_system;

	public function __construct(ParameterBagInterface $params)
	{
		$this->operating_system = $params->get('app.operating_system');
	}

	public function getOperatingSystem(): string
	{
		return $this->operating_system;
	}
}
