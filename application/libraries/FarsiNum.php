<?php

class FarsiNum{
    static private $digits = [
            '0' => '۰',
            '1' => '۱',
            '2' => '۲',
            '3' => '۳',
            '4' => '۴',
            '5' => '۵',
            '6' => '۶',
            '7' => '۷',
            '8' => '۸',
            '9' => '۹',
        ];

    public static function toPrice($number){
        $number = number_format($number);
        return strtr($number, self::$digits);
    }

    public static function toNumber($number){
        return strtr($number, self::$digits);
    }
}