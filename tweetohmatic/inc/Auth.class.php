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