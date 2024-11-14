<?php

declare(strict_types=1);

namespace App\AppBundle\Entity;
use JsonSerializable;

readonly class ConfigurationDto implements JsonSerializable
{
	public function __construct(
		private string $operating_system = PHP_OS_FAMILY,
		private ColorThemeDto $color_theme = new ColorThemeDto(),
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

	public function getColorTheme(): ColorThemeDto
	{
		return $this->color_theme;
	}
}
