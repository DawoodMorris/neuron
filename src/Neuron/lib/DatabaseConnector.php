<?php
/**
**Dawood Morris Kaundama Wed 07 Jul 2021 16:15:27 SAST
**/

/**
 * Connects to a given database
 **/
class DatabaseConnector {
	private string $connectorName;
	private object|bool $connection;
	function __construct(string $connectorName) {
		$this->connectorName = $connectorName;
		$this->connection = '';
	}

	/**
	 * Connect to the database
	 **/
	public function connect(): object|bool {
		$this->connection = new mysqli(_DATABASES['host'],$this->user(),$this->pwd(),$this->dbName());
		if($this->connection->connect_errno) throw new Exception('[DatabaseConnector]: Unable to establish a database connection.', 1);
		$this->connection->set_charset('utf8mb4');
		return $this->connection;
	}

	/**
	 *Determine respective credentials when connectiong to a given database
	 * @return The user name of the database user
	 **/
	private function user(): string{
		return _DATABASES['users'][$this->connectorName];
	}

	private function pwd(): string {
		return _DATABASES['pwds'][$this->connectorName];
	}

	private function dbName(): string {
		return _DATABASES['names'][$this->connectorName];
	}
}


?>