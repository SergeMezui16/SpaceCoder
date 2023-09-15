<?php

namespace App\Twig\Runtime;

use App\Utils\DateFormatUtil;
use Twig\Extension\RuntimeExtensionInterface;

class SinceFormatRuntime implements RuntimeExtensionInterface
{
    public function __construct(){}

    /**
     * Return time flowed since $date formated in french
     *
     * @param \DateTimeImmutable $date
     * @return string
     */
    public function sinceFormat(\DateTimeImmutable $date): string
    {
        return DateFormatUtil::since($date);
    }
}
