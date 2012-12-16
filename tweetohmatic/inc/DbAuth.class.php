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