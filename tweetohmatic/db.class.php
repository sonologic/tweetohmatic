<?php

class Db extends SQLite3 {
	
	public function __construct($file) {
		parent::__construct($file);
		
		$this->query("CREATE TABLE IF NOT EXISTS kv (kv_key text primary key,kv_value text)");
		$this->query("CREATE TABLE IF NOT EXISTS user (username text,password text)");
		$this->query("CREATE TABLE IF NOT EXISTS perm (username text,perm text)");
	}
	
	public function hasPerm($user, $perm) {
		$stmt=$this->prepare("SELECT * FROM perm WHERE username=:user AND perm=:perm");
		$stmt->bindValue(':user',$user);
		$stmt->bindValue(':perm',$perm);
		$result=$stmt->execute();
		if($result->fetchArray())
			return true;
		return false;
	}
	
	public function getPerm($user) {
		$stmt=$this->prepare("SELECT * FROM perm WHERE username=:user");
		$stmt->bindValue(':user',$user);
		$result=$stmt->execute();
		$rv=array();
		while($row=$result->fetchArray(SQLITE3_ASSOC)) {
			array_push($rv,$row['perm']);
		}
		return $rv;
	}
	
	public function getValue($key,$default=false) {
		$stmt=$this->prepare("SELECT * FROM kv WHERE kv_key=:key");
		$stmt->bindValue(':key',$key);
		$result=$stmt->execute();
		if($row=$result->fetchArray()) {
			return $row['kv_value'];
		}
		return $default;
	}
	
	public function setValue($key,$value) {
		$stmt=$this->prepare("INSERT OR REPLACE INTO kv VALUES(:key,:value)");
		$stmt->bindValue(':key',$key);
		$stmt->bindValue(':value',$value);
		$stmt->execute();
	}
	
	public function rmValue($key) {
		
	}
}

?>