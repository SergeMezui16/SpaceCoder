<?php

namespace App\Twig\Runtime;

use Twig\Extension\RuntimeExtensionInterface;

class SinceFormatRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function sinceFormat(\DateTimeImmutable $date)
    {
        return $date->getTimestamp();
    }
}
