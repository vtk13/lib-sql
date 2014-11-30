<?php
namespace Vtk13\LibSql\Mysql;

use mysqli;
use mysqli_result;
use Vtk13\LibSql\IDatabase;
use Vtk13\LibSql\SqlException;

class Mysql implements IDatabase
{
    protected $mysqli;

    public function __construct($host, $user, $pass, $dbName)
    {
        $this->mysqli = new mysqli($host, $user, $pass, $dbName);
        if ($this->mysqli->connect_error) {
            throw new SqlException($this->mysqli->connect_error, $this->mysqli->connect_errno);
        }
    }

    public function escape($str)
    {
        return $this->mysqli->real_escape_string($str);
    }

    public function query($sql)
    {
        if ($this->mysqli->real_query($sql)) {
            return $this->mysqli->affected_rows;
        } else {
            throw $this->err();
        }
    }

    public function select($sql)
    {
        $res = $this->mysqli->query($sql);
        if ($res === false) {
            throw $this->err();
        } elseif ($res === true) {
            $msg = 'Invalid select usage, for non select queries use query method';
            throw new SqlException($msg);
        } else {
            $data = [];
            /* @var $res mysqli_result */
            foreach ($res as $row) {
                $data[] = $row;
            }
            return $data;
        }
    }

    public function selectRow($sql)
    {
        $data = $this->select($sql);
        return isset($data[0]) ? $data[0] : array();
    }

    public function selectValue($sql)
    {
        foreach ($this->selectRow($sql) as $v) {
            return $v;
        }
        return null;
    }

    public function insert($table, $data)
    {
        $sql = "INSERT INTO {$table} SET ";
        $comma = '';
        foreach ($data as $k => $v) {
            $sql .= "{$comma} {$k}='{$this->mysqli->real_escape_string($v)}'";
            $comma = ',';
        }

        static $n = 0;
        $n++;
        $l = strlen($sql);
        $time = microtime(true);
        if ($this->mysqli->real_query($sql)) {
            $time = microtime(true) - $time;
            return $this->mysqli->affected_rows;
        } else {
            throw $this->err();
        }
    }

    public function update($table, $data, $where)
    {
        $sql = "UPDATE {$table} SET ";
        $comma = '';
        foreach ($data as $k => $v) {
            $sql .= "{$comma} {$k}='{$this->mysqli->real_escape_string($v)}'";
            $comma = ',';
        }
        $sql .= " WHERE {$where}";

        if ($this->mysqli->real_query($sql)) {
            return $this->mysqli->affected_rows;
        } else {
            throw $this->err();
        }
    }

    public function delete($from, $where, $what = '')
    {
        if ($this->mysqli->real_query("DELETE {$what} FROM {$from} WHERE {$where}")) {
            return $this->mysqli->affected_rows;
        } else {
            throw $this->err();
        }
    }

    public function insertId()
    {
        return $this->mysqli->insert_id;
    }

    protected function err()
    {
        return new SqlException($this->mysqli->error, $this->mysqli->errno);
    }

    public function where($condition, $value = null)
    {
        if (is_array($condition)) {
            $res = '';
            $and = '';
            foreach ($condition as $field => $value) {
                if ($value === null) {
                    $res .= "{$and}`{$field}` IS NULL";
                } elseif (is_array($value)) {
                    // no check for empty array here
                    $value = "'" . implode("','", array_map([$this, 'escape'], $value)) . "'";
                    $res .= "{$and}`{$field}` IN ({$value})";
                } else {
                    $res .= "{$and}`{$field}`='{$this->escape($value)}'";
                }
                $and = ' AND ';
            }
            return $res;
        } else {
            if ($value === null) {
                return "`{$condition}` IS NULL";
            } elseif (is_array($value)) {
                // no check for empty array here
                $value = "'" . implode("','", array_map([$this, 'escape'], $value)) . "'";
                return "`{$condition}` IN ({$value})";
            } else {
                return "`{$condition}`='{$this->escape($value)}'";
            }
        }
    }
}
