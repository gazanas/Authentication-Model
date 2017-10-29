<?php

class SQL_Model 
{
	private $settings;
	protected $connection;

	public function __construct()
	{
		$this->settings = array(
			"host" => "localhost",
			"user" => ,
			"password" => ,
			"database" =>
		); 
		$this->connection = new mysqli($this->settings["host"], $this->settings["user"], $this->settings["password"], $this->settings["database"]);
	}

	public function getID($username)
	{
		$statement = $this->connection->prepare("SELECT id FROM users WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($id);
		$statement->fetch();
		return $id;
		$statement->close();
	}

	public function getID_by_mail($email)
	{
		$statement = $this->connection->prepare("SELECT id FROM users WHERE email=?");
		$statement->bind_param("s", $email);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($id);
		$statement->fetch();
		return $id;
		$statement->close();
	}

	public function getUpdated($username)
	{
		$statement = $this->connection->prepare("SELECT last_update FROM users WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($last_updated);
		$statement->fetch();
		return $last_updated;
		$statement->close();
	}

	public function getPassword($username)
	{
		$statement = $this->connection->prepare("SELECT password FROM users WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($hash);
		$statement->fetch();
		return $hash;
		$statement->close();
	}

	public function save($username, $email, $password)
	{
		$default = 0;
		$statement = $this->connection->prepare("INSERT INTO users VALUES(NULL, ?, ?, ?, NULL, ?, ?)");
		$statement->bind_param("sssii", $username, $email, $password, $default, $default);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
	}

	public function updatePassword($username, $password)
	{
		$current = date("Y-m-d H:i:s");
		$statement = $this->connection->prepare("UPDATE users SET password=? WHERE user_login=?");
		$statement->bind_param("ss", $password, $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
		$statement = $this->connection->prepare("UPDATE users SET last_update=? WHERE user_login=?");
		$statement->bind_param("ss", $current, $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();	
	}

	public function getTries($username)
	{
		$statement = $this->connection->prepare("SELECT failed_tries FROM users WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($tries);
		$statement->fetch();
		return $tries;
		$statement->close();
	}

	public function incrementTries($username)
	{
		$tries = self::getTries($username);
		$incremented = $tries+1;
		$statement = $this->connection->prepare("UPDATE users SET failed_tries=? WHERE user_login=?");
		$statement->bind_param("is", $incremented, $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
	}

	public function initializeTries($username)
	{
		$init = 0;
		$statement = $this->connection->prepare("UPDATE users SET failed_tries=? WHERE user_login=?");
		$statement->bind_param("is", $init, $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
	}

	public function setLockedTime($username)
	{
		$statement = $this->connection->prepare("UPDATE users SET locked_time=NULL WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
	}

	public function initializeLockedTime($username)
	{
		$statement = $this->connection->prepare("UPDATE users SET locked_time=0 WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->close();
	}

	public function getLockedTime($username)
	{
		$statement = $this->connection->prepare("SELECT locked_time FROM users WHERE user_login=?");
		$statement->bind_param("s", $username);
		$statement->execute();
		if($this->connection->error)
			throw new Exception("Error accessing database.");
		$statement->bind_result($time);
		$statement->fetch();
		return $time;
		$statement->close();
	}
}

?>
