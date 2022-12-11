<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\SinceFormatRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class SinceFormatExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('since', [SinceFormatRuntime::class, 'sinceFormat']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('since', [SinceFormatRuntime::class, 'sinceFormat']),
        ];
    }
}
