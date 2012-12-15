<?php

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