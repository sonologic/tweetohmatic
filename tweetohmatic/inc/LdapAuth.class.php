<?php

/**
 *    This file is part of Tweet-ohm-matic.
 *
 *    Tweet-ohm-matic is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    Tweet-ohm-matic is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with Tweet-ohm-matic.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author "Koen Martens" <gmc@sonologic.nl>
 *
 */

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


