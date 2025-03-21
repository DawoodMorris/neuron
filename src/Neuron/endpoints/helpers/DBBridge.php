<?php

/**
 * Endpoint: DBBridge
 * Contract: Acts as a bridge between the database and the application, to prevent repetitive abstractable database interaction
 * patterns.
 * Required Data: This class instantiates an object which must be passed a database connection
 * Documentation: 
 * @author Dawood F.M Kaundama
 **/
class DBBridge {

	private object $db;
	private array $_rows;

	function __construct(object $db) {
		$this->db = $db;
		$this->results['status'] = false;
		//loadClass(className: 'InputValidator', parentDir: 'endpoints/helpers');
		$this->_rows = [];
	}

	function __destruct() {
		$this->closeDatabaseConnections(databases: []);
	}

	/**
	 * Close database connections if any were defined. Database connections must be explicitly given when being called
	 * if database connections were defined
	 * @param $databases (array) Database connections to close
	 **/
	private function closeDatabaseConnections(array $databases): void {
		if(is_array($databases) && count($databases) > 0) {
			foreach($databases as $database) {
				if(method_exists($database,'close')) {
					$database->close();
				}
			}
		}
	}


	/***********************************************
	 * Action implementations start here...
	 ***********************************************/

	/**
	 * Get table fields for a given table
	 * @param $table (string) The table name
	 * @return $fields (array) The fields of the given table
	 **/
	public function getTableFields(string $table): array {
		if(!$table) {
			$message = 'Invalid input $table. Expected a string, got null.';
			throw new Exception("[[DBBridge] Error attempting to run `getTableFields`]: Error Message: {$message}",1);
		}
		$sql = "DESCRIBE {$table}";
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `DESCRIBE {$table}`]: Error Message: {$message}",1);
		}
		$fields = [];
		$stmt->execute();
		$stmt->bind_result($field,$type,$null,$key,$default,$extra);
		while ($stmt->fetch()) {
			$fields[] = lcfirst($field);
		}
		$stmt->reset();
		return $fields;
	}

	/**
	 * Do a batch SQL insert for more valued queries for fastest execution
	 * @param $sql (string) The batched sql query to execute.
	 * @return bool Whether successful or not.
	 **/
	public function insertBatch(string $sql): bool {
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `insertBatch`]: Error Message: {$message}",1);
		}
		if($stmt->execute()) {
			$stmt->reset();
			return true;
		}
		$message = mysqli_error($this->db);
		$stmt->reset();
		throw new Exception("[[DBBridge] Error attempting to run `insertBatch`]: Error: Message: {$message}",1);
	}

	/**
	 * Set the database connection in order not to re-instantiate the object
	 **/
	public function setDatabase(object $db): void {
		$this->db = $db;
	}

	/**
	 * Update data in a given database
	 * @param $sql (string) The sql statement to run
	 * @param $params (array) The parameters of the sql statement
	 * @return $updated (int|bool) Whether the update succeeded
	 **/
	public function update(string $sql, array $params): bool {
		$types = '';
		foreach($params as $param) {
			$type = gettype($param)[0];
			if($type === 'b') {
				$type = 'i';
			}
			if($type === 'N') {
				$type = 'i';
			}
			$types .= $type;
		}
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `insert`]: Error Message: {$message}",1);
		}
		if(count($params) > 0) {
			$stmt->bind_param($types,...$params);
		} 
		if($stmt->execute()) {
			$stmt->reset();
			return true;
		}
		$message = mysqli_error($this->db);
		$stmt->reset();
		throw new Exception("[[DBBridge] Error attempting to run `update`]: Error: Message: {$message}",1);
	}

	/**
	 * Insert data into a given database
	 * @param $sql (string) The sql statement to run
	 * @param $params (array) The parameters of the sql statement
	 * @return $updated (int|bool) Whether the update succeeded
	 **/
	public function insert(string $sql, array $params): int {
		$types = '';
		foreach($params as $param) {
			$_type = gettype($param)[0];
			if($_type === 'N') {
				$_type = 'i';
			}
			if($_type === 'b') {
				$_type = 'i';
			}
			$types .= $_type;
		}
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `prepare`]: Error Message: {$message}",1);
		}
		if(count($params) > 0) {
			$stmt->bind_param($types,...$params);
		} 
		if($stmt->execute()) {
			$stmt->reset();
			return $stmt->insert_id;
		}
		$message = mysqli_error($this->db);
		$stmt->reset();
		throw new Exception("[[DBBridge] Error attempting to run `insert`]: Error Message: {$message}",1);
	}

	/**
	 * Fetch data from a given database connection and sql query.
	 * @param $sql (string) The sql statement to run
	 * @param $params (array) The parameters of the sql statement
	 * @return $insertId (int|bool) The insert id on success or false otherwise
	 **/
	public function fetch(string $sql, array $params, array $bindTo): object {
		$_bindTo = $bindTo;
		$_results = [];
		$_moreRows = [];
		$types = '';
		foreach($params as $param) {
			$_type = gettype($param)[0];
			if($_type === 'N') {
				$_type = 'i';
			}
			if($_type === 'b') {
				$_type = 'i';
			}
			$types .= $_type;
		}
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `fetch`]: Error Message: {$message}",1);
		}
		if(count($params) > 0) {
			$stmt->bind_param($types,...$params);
		}
		$stmt->bind_result(...$bindTo);
		$stmt->execute();
		$count = 0;
		while ($stmt->fetch()) {
			$count++;
		}
		$stmt->reset();
		if($count > 0) {
			foreach($bindTo as $key => $value) {
				$_results[$_bindTo[$key]] = $value;
			}
		}
		return (object)$_results;
	}

	/**
	 * Fetch data (more than 1 rows) from a given database connection and sql query.
	 * @param $sql (string) The sql statement to run
	 * @param $params (array) The parameters of the sql statement
	 * @return $insertId (int|bool) The insert id on success or false otherwise
	 **/
	public function fetchMany(string $sql, array $params, array $bindTo): object {
		$_bindTo = $bindTo;
		$_rows = [];
		$types = '';
		foreach($params as $param) {
			$_type = gettype($param)[0];
			if($_type === 'N') {
				$_type = 'i';
			}
			if($_type === 'b') {
				$_type = 'i';
			}
			$types .= $_type;
		}
		$stmt = $this->db->prepare($sql);
		if(!$stmt) {
			$message = mysqli_error($this->db);
			throw new Exception("[[DBBridge] Error attempting to run `fetchMany`]: Error Message: {$message}",1);
		}
		if(count($params) > 0) {
			$stmt->bind_param($types,...$params);
		}
		$stmt->execute();
		$stmt->bind_result(...$bindTo);
		while ($stmt->fetch()) {
			$_tempArr = [];
			foreach($_bindTo as $key => $value) {
				$_tempArr[$_bindTo[$key]] = $bindTo[$key];
			}
			$_rows[] = $_tempArr;
		}
		$stmt->reset();
		$this->_rows = $_rows;
		return (object)$_rows;
	}

	/**
	 * Extract many rows into a numeric-indexed array returned by the DBBridge->fetchMany() method.
	 **/
	public function numIndexedArray(): array {
		$items = [];
		foreach($this->_rows as $row) {
			$items[] = array_values($row)[0];
		}
		return $items;
	}

	/**
	 * Extract many rows into a association-indexed array returned by the DBBridge->fetchMany() method.
	 **/
	public function assocIndexedArray(): array {
		$items = [];
		foreach($this->_rows as $row) {
			$items[] = $row;
		}
		return $items;
	}
}
?>