<?php

namespace EasyScrumREST\SprintBundle\Util;

class DateHelper
{
    public static function numberLaborableDays($from, $to) {
        $date =new \DateTime($from->format('Y-m-d'));
        $cont=0;
        while ($date < $to) {
            $day=$date->format('l');
            if ($day!="Sunday" && $day!="Saturday" ) {
                $cont++;
            }
            $date->modify('+1 day');
        }

        return $cont;
    }
}