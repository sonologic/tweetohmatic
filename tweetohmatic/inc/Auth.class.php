<?php

abstract class Auth {
	
	abstract public function authenticate($user,$password);

	abstract public function getUsers();
	
	final public function isAuthenticated() {
		if(isset($_SESSION['authenticated']))
			if($_SESSION['authenticated'])
			return true;
		return false;
	}

}

?>