<?php
namespace App\Utils;

class DateFormatUtil
{
    /**
     * Return time flowed since $date formated in french
     *
     * @param DateTimeImmutable $date
     * @return string
     */
    public static function since(\DateTimeImmutable $date): string
    {
        $duration = time() - $date->getTimestamp();

        if($duration < 0 ) return '';

        $_m = $duration / 60;
        $_h = $_m / 60;
        $_d = $_h / 24;
        $_M = $_d / 30;
        $_y = $_d / 365;

        if($_y >= 1){
            return floor($_y) . ($_y > 1 ? ' ans' : ' an');
        } elseif($_M >= 1) {
            return floor($_M) . ' mois';
        }elseif ($_d >= 1) {
            return floor($_d) . ($_d > 1 ? ' jours' : ' jour');
        } elseif($_h >= 1){
            return floor($_h) . ($_h > 1 ? ' heures' : ' heure');
        }elseif($_m) {
            return floor($_m) . ($_m > 1 ? ' minutes' : ' minute');
        } else {
            return '';
        }
    }


    // donne la date et l'heure en string
    // function date_to_str(string $date, int $format = 1): string
    // {
    //     // format de $date : 'Y/m/d H:i:s'

    //     // tableau des jours et mois en francais
    //     $days = [1 => 'Lun.', 'Mar.', 'Mer.', 'Jeu.', 'Ven.', 'Sam.', 'Dim.'];
    //     $months = [1 => 'Jan.', 'Fev.', 'Mar.', 'Avr.', 'Mai', 'Juin', 'Juillet', 'Aout', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];

    //     // cree une nouvelle date
    //     $dt = new \DateTime($date);

    //     // recupere le numero du jour de la semaine
    //     $day_num = strftime('%u', $dt->getTimestamp());

    //     // recupere les parties de la date
    //     $day = tab_date($date)['day'];
    //     $month = intval(tab_date($date)['month']);
    //     $year = tab_date($date)['year'];
    //     $hour = tab_date($date)['hour'];
    //     $minute = tab_date($date)['minute'];

    //     switch ($format) {
    //         case 1:
    //             return $days[$day_num] . ', le ' . $day . ' ' . $months[$month] . ' ' . $year . ' à ' . $hour . 'H' . $minute;
    //             break;

    //         case 2:
    //             return $day . '/' . $month . '/' . $year . ' • ' . $hour . 'H' . $minute;
    //             break;

    //         default:
    //             return $days[$day_num] . ', le ' . $day . ' ' . $months[$month] . ' ' . $year . ' à ' . $hour . 'H' . $minute;
    //             break;
    //     }
    // } // use example : echo date_to_str('2021-09-17 10:57:01')
}