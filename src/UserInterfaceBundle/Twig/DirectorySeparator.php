<?php

declare(strict_types=1);

namespace App\UserInterfaceBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DirectorySeparator extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('directory_separator', fn() => DIRECTORY_SEPARATOR),
            new TwigFunction('ends_with', [$this, 'endsWith']),
        ];
    }

    public function endsWith(string $haystack, string $needle): bool
    {
        return str_ends_with($haystack, $needle);
    }
}