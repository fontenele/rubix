<?php

namespace FS\Types;

class ArrayUtil {

    public static function sprintf($data, $args) {
        $arrNew = array();

        foreach ($data as $_attr => $_val) {
            if (is_array($_val)) {
                $arrNew[$_attr] = self::sprintf($_val, $args);
            } else {
                switch (true) {
                    case strpos($_val, '%b') > 0 || strpos($_val, '%d') === 0:
                    case strpos($_val, '%c') > 0 || strpos($_val, '%c') === 0:
                    case strpos($_val, '%d') > 0 || strpos($_val, '%d') === 0:
                    case strpos($_val, '%e') > 0 || strpos($_val, '%e') === 0:
                    case strpos($_val, '%E') > 0 || strpos($_val, '%E') === 0:
                    case strpos($_val, '%u') > 0 || strpos($_val, '%u') === 0:
                    case strpos($_val, '%f') > 0 || strpos($_val, '%f') === 0:
                    case strpos($_val, '%F') > 0 || strpos($_val, '%F') === 0:
                    case strpos($_val, '%g') > 0 || strpos($_val, '%g') === 0:
                    case strpos($_val, '%G') > 0 || strpos($_val, '%G') === 0:
                    case strpos($_val, '%o') > 0 || strpos($_val, '%o') === 0:
                    case strpos($_val, '%s') > 0 || strpos($_val, '%s') === 0:
                    case strpos($_val, '%x') > 0 || strpos($_val, '%x') === 0:
                    case strpos($_val, '%X') > 0 || strpos($_val, '%X') === 0:
                        $_val = sprintf(array_shift($args));
                        break;
                }
                $arrNew[$_attr] = $_val;
            }
        }

        return $arrNew;
    }

    public static function orderBy($attr, $array, $type = SORT_NUMERIC) {

        $_array = array();

        foreach ($array as $_cod => $_value) {
            if (isset($_value['items']) && is_array($_value['items'])) {

                if (isset($_value[$attr])) {
                    $_array[$_value[$attr]] = $_value;
                    $_array[$_value[$attr]]['items'] = self::orderBy($attr, $_value['items']);
                }
            } else {
                if (isset($_value[$attr])) {
                    $_array[$_value[$attr]] = $_value;
                } else {
                    $_array[$_cod] = $_value;
                }
            }
        }

        ksort($_array, $type);
        return $_array;
    }

}
