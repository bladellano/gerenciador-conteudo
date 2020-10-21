<?php 

namespace Source\DB;

class Sql {

	/* const HOSTNAME = "127.0.0.1";
	const USERNAME = "admin";
	const PASSWORD = "admin";
	const DBNAME = "db_ecommerce_editado"; */

	const HOSTNAME = DB_SITE['host'];
	const USERNAME = DB_SITE['username'];
	const PASSWORD = DB_SITE['passwd'];
	const DBNAME = DB_SITE['dbname'];

	private $conn;

	public function __construct()
	{

		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD
		);

	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{

		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

		$stmt->execute();

	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);
		
		$stmt->execute();

		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>