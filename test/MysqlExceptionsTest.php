<?php
use Vtk13\LibSql\IDatabase;
use Vtk13\LibSql\Mysql\Mysql;

class MysqlExceptionsTestClass extends PHPUnit_Framework_TestCase
{
    /**
     * @var IDatabase
     */
    protected $db;

    public function setUp()
    {
        $this->db = new Mysql('localhost', 'root', '', 'test');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage php_network_getaddresses: getaddrinfo failed: Name or service not known
     */
    public function testConnectWrongHost()
    {
        @new Mysql('qwe', '', '', '');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage Access denied for user 'root'@'localhost' (using password: YES)
     */
    public function testConnectWrongUser()
    {
        @new Mysql('localhost', 'root', 'qweqwe', '');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage Unknown database 'werwer'
     */
    public function testConnectUnknownDatabase()
    {
        @new Mysql('localhost', 'root', '', 'werwer');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage You have an error in your SQL syntax;
     */
    public function testMisspelledSelect()
    {
        $this->db->select('S E');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage You have an error in your SQL syntax;
     */
    public function testMisspelledInsert()
    {
        $this->db->insert('a b c', array());
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage You have an error in your SQL syntax;
     */
    public function testMisspelledUpdate()
    {
        $this->db->update('S E', array(), 'q');
    }

    /**
     * @expectedException \Vtk13\LibSql\SqlException
     * @expectedExceptionMessage You have an error in your SQL syntax;
     */
    public function testMisspelledDelete()
    {
        $this->db->delete('S E', 'q w');
    }
}
