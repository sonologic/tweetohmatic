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

$debug = false;

$dbpath = 'private/db.sqlite';

define('AUTH_METHOD', 'db');
//define('AUTH_METHOD', 'ldap');

//define('LDAP_HOST', 'ldaps://localhost/');
//define('LDAP_ROOT', 'ou=users,dc=yoyodyne,dc=com');
//define('LDAP_ATTR', 'uid');
//define('LDAP_FILTER', '(objectClass=posixAccount)');
//define("LDAP_BIND_DN", "uid=tweetohmatic,".LDAP_ROOT);
//define("LDAP_BIND_PW", "secret");

?>
