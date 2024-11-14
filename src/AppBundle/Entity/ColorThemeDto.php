<?php

declare(strict_types=1);

namespace App\AppBundle\Entity;
use JsonSerializable;

readonly class ColorThemeDto implements JsonSerializable
{
	public function __construct(
		private string $background = '#141517',
		private string $background_secondary = '#19191c',
		private string $text = '#cbd5e1',
		private string $text_secondary = '#cbd5e2',
	)
	{

	}

	public function jsonSerialize(): array
	{
		return [
			'background' => $this->background,
			'background_secondary' => $this->background_secondary,
			'text' => $this->text,
			'text_secondary' => $this->text_secondary,
		];
	}

	public function getBackgroundAccent(): string
	{
		return $this->background;
	}

	public function getBackgroundSecondaryAccent(): string
	{
		return $this->background_secondary;
	}

	public function getTextAccent(): string
	{
		return $this->text;
	}

	public function getTextSecondaryAccent(): string
	{
		return $this->text_secondary;
	}
}
