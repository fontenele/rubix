<?php

namespace FS\View\Helper;

use DateTime;
//use IntlDateFormatter;
//use Locale;
use Zend\I18n\Exception;
use Zend\View\Helper\AbstractHelper;

class DateFormat extends AbstractHelper {

    /**
     *
     * @param string $datetime
     * @param int $timestamp
     * @param string $format
     * @return string
     * @throws Exception
     */
    public function __invoke($datetime = null, $timestamp = null, $format = 'd-m-Y') {

        $formated = '';

        if ($datetime) {
            $arrDate = explode(' ', $datetime);
            list($date, $time) = $arrDate;
            $date = implode('/', array_reverse(explode('-', $date)));
            $time = substr($time, 0, 8);
            $formated = $date . ' ' . $time;
        } elseif ($timestamp) {
            $formated = date($format, $date);
        } else {
            throw new \Exception('Nenhuma data foi informada.');
        }

        return $formated;
    }

    public static function getDatetime($datetime, $timestamp = null, $format = 'd-m-Y') {
        $formated = '';
        if ($datetime) {
            $arrDate = explode(' ', $datetime);
            list($date, $time) = $arrDate;
            $date = implode('/', array_reverse(explode('-', $date)));
            $time = substr($time, 0, 8);
            $formated = $date . ' ' . $time;
        } elseif ($timestamp) {
            $formated = date($format, $date);
        } else {
            //throw new \Exception('Nenhuma data foi informada.');
        }

        return $formated;
    }

    public static function prepareForSave($datetime) {
        $datetime = explode(' ', $datetime);
        $date = array_reverse(explode('/', $datetime[0]));
        $time = explode(':', $datetime[1]);
        if (!isset($time[2])) {
            $time[] = '00';
        }
        return implode('-', $date) . ' ' . implode(':', $time);
    }

}
