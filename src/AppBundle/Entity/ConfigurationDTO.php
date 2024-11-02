<?php

declare(strict_types=1);

namespace App\AppBundle\Entity;
use JsonSerializable;

class ConfigurationDTO implements JsonSerializable
{
	public function __construct(
		private readonly string $operating_system = PHP_OS_FAMILY,
		private readonly array $color_theme = [
			'background' => '#141517',
			'background-secondary' => '#19191c',
			'text' => '#cbd5e1',
			'text-secondary' => '#71717a'
		]
	)
	{

	}

	public function jsonSerialize(): array
	{
		return [
			'operating_system' => $this->operating_system,
			'color_theme' => $this->color_theme,
		];
	}

	public function getOperatingSystem(): string
	{
		return $this->operating_system;
	}

	public function getColorTheme(): array
	{
		return $this->color_theme;
	}
}
