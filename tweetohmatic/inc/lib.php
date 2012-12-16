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

define('PERMISSIONS',"tweet,user_admin,moderate,twitter_account");

function error_handler($errno, $errstr, $errfile, $errline, $errcontext) {
	global $debug;

	$response = array('error'=>'An error occured.');

	if($debug) {
		$response['errno']=$errno;
		$response['errstr']=$errstr;
		$response['errfile']=$errfile;
		$response['errline']=$errline;
		$response['errcontext']=$errcontext;
	}

	echo json_encode($response);

	die();
}

function exception_handler($exception) {
	global $debug;

	$response = array('error'=>'An error occured.');

	if($debug) {
		$response['exception']=utf8_encode(var_export($exception,true));
	}

	echo json_encode($response);

	die();
}

set_error_handler("error_handler");
set_exception_handler("exception_handler");

session_start();

?>