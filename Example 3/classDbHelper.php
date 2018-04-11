<?php

// Robert Sims - Web Dev Candidate test.

class classDbHelper {
	private $host = '';
	private $user = '';
	private $pass = '';
	public $db = '';
	public $conn;
	public $userid;
	public $rowCount;

	function __construct($_host, $_user, $_pass, $_db ) {
		$this->host = $_host;
		$this->user = $_user;
		$this->pass = $_pass;
		$this->db 	= $_db;

		$this->connectToDatabase();
	}

	public function connectToDatabase() {

		$this->conn = new mysqli(
			$this->host,
			$this->user,
			$this->pass,
			$this->db
		);
		
	}

	public function selectData($_qry) {
		$_conn = $this->conn;
		$_result = $_conn->query($_qry);

		return $_result;
	}

	public function insertData($_qry) {
		$_conn = $this->conn;
		$_result = $_conn->query($_qry);

		return $_conn->insert_id;
	}

	public function getCount($_qry) {
		$_conn = $this->conn;
		$_result = $_conn->query($_qry);

		$this->rowCount = $_result->num_rows;
		return $this->rowCount;
	}

	public function cleanString($_str) {
		$_conn = $this->conn;
		$_str = $_conn->real_escape_string($_str);

		return $_str;
	}
}

?>