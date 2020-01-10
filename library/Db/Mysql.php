<?php
require __DIR__ . '/medoo.php';
class Db_Mysql extends medoo {
    protected $debug = false;

    public function __construct($options = null) {
        $this->debug = isset($options['debug']) ? $options['debug'] : false;
        if(is_array($options)) {
            unset($options['debug']);
        }
        parent::__construct($options);
    }

	protected function select_context($table, $join, &$columns = null, $where = null, $column_fn = null) {
        $foundRows = false;
        if(is_array($where) && isset($where['FOUND_ROWS'])) {
            $foundRows = $where['FOUND_ROWS'];
            unset($where['FOUND_ROWS']);
        }
        if(is_array($columns) && isset($columns['FOUND_ROWS'])) {
            $foundRows = $columns['FOUND_ROWS'];
            unset($columns['FOUND_ROWS']);
        }
        $sql = parent::select_context($table, $join, $columns, $where, $column_fn);
        return $foundRows ? ("SELECT SQL_CALC_FOUND_ROWS " . substr($sql, 7)) : $sql;
    }

    public function found_rows() {
        $ret = $this->query("select FOUND_ROWS()")->fetchColumn();
        return is_numeric($ret) ? $ret + 0 : 0;
    }

	public function batch_insert($table, $datas) {
		// Check indexed or associative array
		if (!isset($datas[0])) {
			$datas = array($datas);
		}
        $all_values = array();
		foreach ($datas as $data) {
			$values = array();
			foreach ($data as $key => $value) {
				switch (gettype($value)) {
					case 'NULL':
						$values[] = 'NULL';
						break;

					case 'array':
						preg_match("/\(JSON\)\s*([\w]+)/i", $key, $column_match);

						$values[] = isset($column_match[0]) ?
							$this->quote(json_encode($value)) :
							$this->quote(serialize($value));
						break;

					case 'boolean':
						$values[] = ($value ? '1' : '0');
						break;

					case 'integer':
					case 'double':
					case 'string':
						$values[] = $this->fn_quote($key, $value);
						break;
				}
			}
            $all_values[] = '(' . join (', ', $values) . ')';
		}
        $sql = join(",", $all_values);
        $columns = array_map(array($this, "column_quote"), array_keys($datas[0]));
        $this->exec('INSERT INTO "' . $table . '" (' . implode(', ', $columns) . ') VALUES ' . $sql);
        $lastId = $this->pdo->lastInsertId();

        $lastIds = array();
        for($i = 0, $count = count($datas); $i < $count; $i++) {
            $lastIds[] = $lastId + $i; 
        }
		return $lastIds;
	}

	protected function fn_quote($column, $string) {
        return isset($column[0]) && $column[0] == '#' ? $string : $this->quote($string);
	}

	public function query($query) {
		$this->debug && array_push($this->logs, $query);
		return $this->pdo->query($query);
	}

	public function exec($query) {
		$this->debug && array_push($this->logs, $query);
		return $this->pdo->exec($query);
	}
    
    public function begin() {   
        $this->pdo->beginTransaction();
    }

    public function commit() {
        $this->pdo->commit();
    }

    public function rollback() {
        $this->pdo->rollback();
    }

    public function distinct($table, $join = null, $column = null, $where = null) {
        return $this->query($this->select_context($table, $join, $column, $where, 'DISTINCT'))->fetchAll();
    }

    public function getPdo() {
        return $this->pdo;
    }

    public function __destruct() {
        if($this->pdo->inTransaction()) {
            $this->pdo->rollback();
        }
    }
}
