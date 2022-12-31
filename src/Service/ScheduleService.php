<?php 
namespace App\Service;


class ScheduleService
{
    public function isNewYear(): bool
    {
        return date('d') === '01' && date('m') === '01';
    }
}