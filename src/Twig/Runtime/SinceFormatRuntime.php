<?php

namespace App\Twig\Runtime;

use App\Utils\DateFormatUtil;
use Twig\Extension\RuntimeExtensionInterface;

class SinceFormatRuntime implements RuntimeExtensionInterface
{
    public function __construct()
    {
        // Inject dependencies if needed
    }

    public function sinceFormat(\DateTimeImmutable $date)
    {
        return DateFormatUtil::since($date);
    }
}
