<?php

require_once(dirname(__FILE__)."/Auth.class.php");

class LdapAuth extends Auth {
	private $conn;
	
	public function __construct() {
		$this->conn = ldap_connect(LDAP_HOST);
		if(!$this->conn)
			throw new Exception("Unable to connect to ldap host.");
		ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, 3);
	}
	
	public function authenticate($user, $password) {
		if(ldap_bind($this->conn, LDAP_ATTR."=$user,".LDAP_ROOT, $password)) {
			$_SESSION['authenticated']=time();
			$_SESSION['user']=$user;
			return true;
		} else {
			return false;
		}			
	}
	
	public function getUsers() {
		$users = array();

		ldap_bind($this->conn,LDAP_BIND_DN,LDAP_BIND_PW);
	
		$result = ldap_search(
			$this->conn,
			LDAP_ROOT,
			LDAP_FILTER,
			array(LDAP_ATTR)
		);
		if($result) {
			$entries=ldap_get_entries($this->conn, $result);
			for($i=0;$i<$entries['count'];$i++)
				array_push($users,$entries[$i]['uid'][0]);
		}
		return $users;
	}
}


