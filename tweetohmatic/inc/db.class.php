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


class Db extends SQLite3 {
	
	public function __construct($file) {
		parent::__construct($file);
		
		$this->query("CREATE TABLE IF NOT EXISTS kv (kv_key text primary key,kv_value text)");
		$this->query("CREATE TABLE IF NOT EXISTS user (username text,password text)");
		$this->query("CREATE TABLE IF NOT EXISTS perm (username text,perm text)");
		$this->query("CREATE TABLE IF NOT EXISTS queue (username text,ts int,status text)");
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
	
	public function setPerm($user,$perm) {
		foreach(preg_split('/,/',PERMISSIONS) as $p) {
			if(array_search($p,$perm)!==false) {
				$stmt=$this->prepare("INSERT OR REPLACE INTO perm VALUES (:user,:perm)");
			} else {
				$stmt=$this->prepare("DELETE FROM perm WHERE username=:user AND perm=:perm");
			}
			$stmt->bindValue(':perm',$p);
			$stmt->bindValue(':user',$user);
			$stmt->execute();
		}
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