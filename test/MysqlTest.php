<?php
use Vtk13\LibSql\IDatabase;
use Vtk13\LibSql\Mysql\Mysql;

class MysqlTestClass extends PHPUnit_Framework_TestCase
{
    /**
     * @var IDatabase
     */
    protected $db;

    public function setUp()
    {
        $this->db = new Mysql('localhost', 'root', '', 'test');
    }

    public function testConnect()
    {
        $this->assertTrue($this->db instanceof IDatabase);
    }

    public function testSelect()
    {
        $data = $this->db->select('SELECT * FROM test_users');
        $this->assertEquals(3, count($data));

        $row = $this->db->selectRow('SELECT * FROM test_users WHERE id=1');
        $this->assertEquals(1, $row['id']);
        $this->assertEquals('user1', $row['username']);
        $this->assertEquals('pass', $row['password']);

        $count = $this->db->selectValue('SELECT COUNT(*) FROM test_users');
        $this->assertEquals(3, $count);
    }

    public function testUpdate()
    {
        $where = 'username="user3"';
        $pass = $this->db->selectValue("SELECT password FROM test_users WHERE {$where}");

        $rows = $this->db->update('test_users', array('password' => $pass . '1'), $where);
        $this->assertEquals(1, $rows);

        $rows = $this->db->update('test_users', array('password' => $pass), $where);
        $this->assertEquals(1, $rows);
    }

    public function testInsertDelete()
    {
        $rows = $this->db->insert('test_users', array(
            'username'  => 'user4',
            'password'  => 'password',
        ));
        $this->assertEquals(1, $rows);
        $this->assertGreaterThan(3, $this->db->insertId());

        $row = $this->db->selectRow('SELECT * FROM test_users WHERE username="user4"');
        $this->assertEquals('user4', $row['username']);
        $this->assertEquals('password', $row['password']);

        $rows = $this->db->query('DELETE FROM test_users WHERE username="user4"');
        $this->assertEquals(1, $rows);

        $count = $this->db->selectValue("SELECT COUNT(*) FROM test_users WHERE username='user4'");
        $this->assertEquals(0, $count);
    }

    public function testConditionBuilder()
    {
        $this->assertEquals('`a` IS NULL', $this->db->where('a', null));
        $this->assertEquals("`a`='4'", $this->db->where('a', 4));
        $this->assertEquals("`a` IN ('1','2','3')", $this->db->where('a', [1,2,3]));

        $this->assertEquals('`a` IS NULL', $this->db->where(array('a' => null)));
        $this->assertEquals("`a`='4'", $this->db->where(array('a' => 4)));
        $this->assertEquals("`a` IN ('1','2','3')", $this->db->where(array('a' => [1,2,3])));
        $this->assertEquals("`a`='4' AND `b`='as\\'d'", $this->db->where(array('a' => 4, 'b' => 'as\'d')));
    }
}
