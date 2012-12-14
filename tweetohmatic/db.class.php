<?php

class Db extends SQLite3 {
	
	public function __construct($file) {
		parent::__construct($file);
		
		$this->query("CREATE TABLE IF NOT EXISTS kv (kv_key text,kv_value text)");
		$this->query("CREATE TABLE IF NOT EXISTS user (username text,password text)");
	}
}

?>