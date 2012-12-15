<?php

require_once(dirname(__FILE__)."/Auth.class.php");

class LdapAuth extends Auth {
	privat $conn;
	
	public function __construct() {
		$this->conn = ldap_connect(LDAP_HOST);
		if(!$this->conn)
			throw new Exception("Unable to connect to ldap host.");
	}
	
	public function authenticate($user, $password) {
		if(ldap_bind($this->conn, "cn=$user,".LDAP_ROOT, $password)) {
			$_SESSION['authenticated']=time();
			$_SESSION['user']=$row['username'];
			return true;
		} else {
			return false;
		}			
	}
	
}

?>