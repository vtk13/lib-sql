<?php
namespace Vtk13\LibSql;

interface IDatabase
{
    /**
     * @param string $str
     * @return string
     */
    public function escape($str);

    /**
     * @param string $sql
     * @return int affected rows
     */
    public function query($sql);

    /**
     * @param string $sql
     * @return mixed[][]
     */
    public function select($sql);

    /**
     * @param string $sql
     * @return mixed[]
     */
    public function selectRow($sql);

    /**
     * @param string $sql
     * @return mixed
     */
    public function selectValue($sql);

    /**
     * @param string $table
     * @param array $data
     * @return int affected rows
     */
    public function insert($table, $data);

    /**
     * @param string $table
     * @param array $data
     * @param string $where
     * @return int affected rows
     */
    public function update($table, $data, $where);

    /**
     * @param string $from
     * @param string $where
     * @param string $what
     * @return int affected rows
     */
    public function delete($from, $where, $what = '');

    /**
     * @return int
     */
    public function insertId();

    public function where($condition, $value = null);
}
