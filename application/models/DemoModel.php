<?php
/**
 * @description
 *
 * @author songxk
 * @version V1.0.0
 */
class DemoModel extends ModelBase {
    protected $_table = 'table name';
    protected $_primary = 'primary id';

    public function findUser($userId) {
        $where = [
            'user_id' => $userId,
        ];
        return $this->fetchRow($where);
    }
}