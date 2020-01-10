<?php
class ModelBase {
    protected $_table;
    protected $_primary;

    public function __construct() {
        if($this->_table === null || $this->_primary === null) {
            throw new Db_Exception(__CLASS__ . ": table name or primary key cannot be empty");
        }
        $this->_primary = is_array($this->_primary) ? $this->_primary : array($this->_primary);
    }

    private function getDb() {
        return Db_Manager::getInstance();
    }

    public function find($id) {
        $ids = is_array($id) ? $id : array($id);
        if(count($ids) != count($this->_primary)) {
            throw new Db_Exception("values count and primary key count not match for {$this->_table}");
        }
        $where = array();
        foreach($ids as $key => $val) {
            $where[$this->_primary[$key]] = $val;
        }
        return $this->getDb()->get($this->_table, '*', array("AND" => $where));
    }

    public function fetchRow($where, $fields = '*', $join = null) {
        if(is_null($join)) {
            return $this->getDb()->get($this->_table, $fields, $where);
        } else {
            return $this->getDb()->get($this->_table, $join, $fields, $where);
        }
    }

    public function fetchOne($where, $field = '', $join = null) {
        if(is_null($join)) {
            return $this->getDb()->get($this->_table, $field, $where);
        } else {
            return $this->getDb()->get($this->_table, $join, $field, $where);
        }
    }

    public function fetchAll($where, $fields = '*', $join = null) {
        if(is_null($join)) {
            return $this->getDb()->select($this->_table, $fields, $where) ?: array();
        } else {
            return $this->getDb()->select($this->_table, $join, $fields, $where) ?: array();
        }
    }

    public function foundRows() {
        return $this->getDb() ? $this->getDb()->found_rows() : 0;
    }

    public function insert($data) {
        return $this->getDb()->insert($this->_table, $data);
    }

    public function batchInsert($datas) {
        return $this->getDb()->batch_insert($this->_table, $datas);
    }

    public function update($data, $where) {
        return $this->getDb()->update($this->_table, $data, $where);
    }

    public function delete($where) {
        return $this->getDb()->delete($this->_table, $where);
    }

    public function query($sql) {
        return $this->getDb()->query($sql);
    }

    public function makeWhere($where, $id = null) {
        return $this->_getWriter($this->_farm($id))->makeWhere($where);
    }

    /**
     *
     * @param $sql
     * @param $id
     * @return 第一列的value
     */
    public function queryColumn($sql, $id = NULL) {
        return $this->query($sql, $id)->fetchColumn();
    }

    public function queryAll($sql, $id = NULL, $fetchStyle = \PDO::FETCH_ASSOC) {
        return $this->query($sql, $id)->fetchAll($fetchStyle);
    }

    public function count($where) {
        return $this->getDb()->count($this->_table, $where);
    }

    public function __call($method, $args) {
        $methods = array("avg", "min", "count", "max", "sum", "log", "error", "last_query", "distinct");
        if(in_array($method, $methods)) {
            array_unshift($args, $this->_table);
            $obj = $this->getDb();
            return call_user_func_array(array($obj, $method), $args);
        }
        throw new Db_Exception(get_called_class() . " has no method $method");
    }

    public function __destruct() {
    }
}