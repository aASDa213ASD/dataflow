<?php

declare(strict_types=1);

namespace App\AppBundle\Service;

use App\AppBundle\Entity\ColorThemeDto;
use App\AppBundle\Entity\ConfigurationDto;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use RuntimeException;

class ConfigService
{
	private ConfigurationDto $config;
	private Filesystem $filesystem;
	private string $config_file;

	public function __construct(ParameterBagInterface $params)
	{
		$this->config_file = $params->get('kernel.project_dir') . '/config/settings.json';
		$this->filesystem = new Filesystem();
		$this->config = $this->load();
	}

	public function get(): ConfigurationDto
	{
		return $this->config;
	}

	public function save(ConfigurationDto $config): void
	{
		$this->config = $config;
		$payload = json_encode($config, JSON_PRETTY_PRINT);

		if ($payload === false)
		{
			throw new RuntimeException('Failed to serialize configuration.');
		}

		$this->filesystem->dumpFile($this->config_file, $payload);
	}

	public function load(): ConfigurationDto
	{
		if ($this->filesystem->exists($this->config_file))
		{
			$payload = json_decode(file_get_contents($this->config_file), true);

			if (json_last_error() === JSON_ERROR_NONE)
			{
				return new ConfigurationDto(
					$payload['operating_system'],
					new ColorThemeDto(...$payload['color_theme'])
				);
			}
		}

		return new ConfigurationDto();
	}
}
