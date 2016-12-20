<?php
/*	
 * With reference to https://github.com/auraphp/Aura.Sql
 * 
 * */
namespace Lib\Support\SqlServer;

use Aura\Sql\ExtendedPdo;
use Aura\Sql\Exception\MissingParameter;

class SqlServer 
{
	private $db;
	
	public function __construct()
	{
		if ( !in_array("pdo_dblib", get_loaded_extensions()) )
		{
			throw new MissingParameter("not exist pdo_dblib extensions error.");
		}
		 
		$this->db = new ExtendedPdo("dblib:host=10.1.11.225:1433;dbname=a1", "oa", "oa@putao.com");
		// $this->db->connect();
	}
	
	public function select(String $query, Array $bind = array())
	{
		// $result = $this->db->fetchAll($query, $bind);
		$result = $this->db->fetchObjects($query, $bind, 'StdClass', array());
		return $result;
	}
	
	public function selectOne(String $query, Array $bind = array())
	{
		// $result = $this->db->fetchOne($query, $bind);
		$result = $this->db->fetchObject($query, $bind, 'StdClass', array());
		return $result;
	}
	
	public function __destruct()
	{
		$this->db->disconnect();
	}
}
	