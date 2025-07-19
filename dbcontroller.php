<?php
class DBController
{
	private $host = "localhost";
	private $user = "idigueco";
	private $password = "op00Zd24Yv";
	private $database = "idigueco_idigue";
	private $conn;

	function __construct()
	{
		$this->conn = $this->connectDB();
	}

	function connectDB()
	{
		$conn = mysqli_connect($this->host, $this->user, $this->password, $this->database);
		return $conn;
	}

	function runQuery($query)
	{
		$result = mysqli_query($this->conn, $query);
		while ($row = mysqli_fetch_assoc($result)) {
			$resultset[] = $row;
		}
		if (!empty($resultset))
			return $resultset;
	}

	function numRows($query)
	{
		$result  = mysqli_query($this->conn, $query);
		$rowcount = mysqli_num_rows($result);
		return $rowcount;
	}
}
