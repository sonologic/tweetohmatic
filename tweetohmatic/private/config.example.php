<?php

$debug = true;

$dbpath = 'private/db.sqlite';

define('AUTH_METHOD', 'db');
//define('AUTH_METHOD', 'ldap');

define('LDAP_HOST', 'ldaps://localhost/');
define('LDAP_ROOT', 'ou=users,dc=yoyodyne,dc=com');

?>