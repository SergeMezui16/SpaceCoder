<?php 
namespace App\Service;


/**
 * Calendar Service
 * 
 * This Service manage schedule of all events
 */
class ScheduleService
{
    /**
     * Check if we are the first day of the year
     *
     * @return boolean
     */
    public function isNewYear(): bool
    {
        return date('d') === '01' && date('m') === '01';
    }
}