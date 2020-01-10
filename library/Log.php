<?php

class Log {
    public static function _log($log) {
        if (!$log) {
            return false;
        }
        if(is_array($log)) {
            $log = json_encode($log);
        }
        Log_File::_instance()->write($log);
    }
}