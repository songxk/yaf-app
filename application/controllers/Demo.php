<?php
/**
 * @description
 *
 * @author songxk
 * @version V1.0.0
 */

class Demo extends ControllerBase {

    public function test() {
        $userId = $this->getParam( 'user_id', 0 );
        try {
            $ret = $userId;
            return $this->_renderJson($ret);
        } catch ( Exception $e ) {
            return $this->_exception( $e, __METHOD__, __LINE__ );
        }
    }
}