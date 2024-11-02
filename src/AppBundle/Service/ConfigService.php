<?php

declare(strict_types=1);

namespace App\AppBundle\Service;

use App\AppBundle\Entity\ConfigurationDTO;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;
use RuntimeException;

class ConfigService
{
	private string $config_file;
	private ConfigurationDTO $config;
	private Filesystem $filesystem;

	public function __construct(ParameterBagInterface $params)
	{
		$this->config_file = $params->get('kernel.project_dir') . '/config/settings.json';

		$this->filesystem = new Filesystem();
		$this->config = $this->loadConfig();
	}

	public function getConfig(): ConfigurationDTO
	{
		return $this->config;
	}

	public function saveConfig(ConfigurationDTO $config): void
	{
		$this->config = $config;
		$config_data = json_encode($config, JSON_PRETTY_PRINT);

		if ($config_data === false)
		{
			throw new RuntimeException('Failed to serialize configuration.');
		}

		$this->filesystem->dumpFile($this->config_file, $config_data);
	}

	public function loadConfig(): ConfigurationDTO
	{
		if ($this->filesystem->exists($this->config_file))
		{
			$config_data = json_decode(file_get_contents($this->config_file), true);

			if (json_last_error() === JSON_ERROR_NONE)
			{
				return new ConfigurationDTO(...$config_data);
			}
		}

		return new ConfigurationDTO();
	}
}
