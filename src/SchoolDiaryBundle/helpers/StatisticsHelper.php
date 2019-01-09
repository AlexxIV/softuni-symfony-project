<?php

namespace SchoolDiaryBundle\helpers;


class StatisticsHelper
{
    public static function calculate_median($arr) {
        $count = count($arr); //total numbers in array
        if ($count == 0) {
            return $arr;
        }
        $middleval = floor(($count-1)/2); // find the middle value, or the lowest middle value
        if($count % 2) { // odd number, middle is the median
            $median = $arr[$middleval];
        } else { // even number, calculate avg of 2 medians
            $low = $arr[$middleval];
            $high = $arr[$middleval+1];
            $median = (($low+$high)/2);
        }
        return $median;
    }

    public static function calculate_average($arr) {
        $total = 0;
        $count = count($arr); //total numbers in array
        if ($count == 0) {
            return $arr;
        }
        foreach ($arr as $value) {
            $total += $value; // total value of array numbers
        }
        return $total/$count;
    }
}