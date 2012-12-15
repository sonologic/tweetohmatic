<?php

require_once(dirname(__FILE__)."/Auth.class.php");

class DbAuth extends Auth {
	private $db;
	
	public function __construct($db) {
		$this->db=$db;
	}
	
	public function getUsers() {
		$rv=array();
		$stmt = $this->db->prepare("SELECT * FROM user");
		$result = $stmt->execute();
		while($row=$result->fetchArray())
			array_push($rv, $row['username']);
		return $rv;
	}
	
	public function authenticate($user,$password) {
		$stmt = $this->db->prepare("SELECT * FROM user WHERE username=:user AND password=:pass");
		$stmt->bindValue(':user',$_REQUEST['u'],SQLITE3_TEXT);
		$stmt->bindValue(':pass',hash('sha512',$_REQUEST['p']),SQLITE3_TEXT);
		$result = $stmt->execute();
		if($row=$result->fetchArray()) {
			$_SESSION['authenticated']=time();
			$_SESSION['user']=$row['username'];
			return true;
		} else {
			return false;
		}
	}
	
}

?>