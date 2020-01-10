<?php
class ControllerBase extends Yaf_Controller_Abstract {
    const RET_NOT_FOUND = 0;
    const RET_OK = 1;
    const RET_CODE_UNKNOWN = 500;

    protected function getParam($name, $default = false) {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    protected function _renderJson($result) {
        header('Content-Type: application/json');
        $ret = [
            'ret_code' => 200,
            'result' => $result,
            'ret_msg' => '',
        ];
        echo json_encode($ret);
        exit;
    }

    protected function _exception(Exception $e, $method, $line, $msg = '') {
        Log::_log($method.'['.$line.']'.':'.$e->getMessage());
        $retMsg = $msg ?: $e->getMessage();
        $retCode = $e->getCode();
        $retCode = $retCode ?: self::RET_CODE_UNKNOWN;
        $ret = [
            'ret_code' => $retCode,
            'result' => '',
            'ret_msg' => $retMsg
        ];
        echo json_encode($ret);
    }
}
