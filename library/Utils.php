<?php

class Utils {
    public static function insertArray($array, $pos, $value) {
        return array_merge(array_slice($array, 0, $pos - 1), array($value), array_slice($array, $pos - 1));
    }
}