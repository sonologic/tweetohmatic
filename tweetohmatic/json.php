<?php

require_once('lib.php');
require_once('db.class.php');

if(!isset($_REQUEST['c']))
	die(json_encode(array('error'=>'Parameter error')));

$db = new Db('private/db.sqlite');

switch($_REQUEST['c']) {
	case 'ia': // is_authenticated
		$rv=array('auth'=>0);		
		if(isset($_SESSION['authenticated']))
			if($_SESSION['authenticated'])
				$rv=array('auth'=>1);
		break;
	case 'a': // authenticate
		$stmt = $db->prepare("SELECT * FROM user WHERE username=:user AND password=:pass");
		$stmt->bindValue(':user',$_REQUEST['u'],SQLITE3_TEXT);
		$stmt->bindValue(':pass',hash('sha512',$_REQUEST['p']),SQLITE3_TEXT);
		$result = $stmt->execute();
		if($result->fetchArray())
			$rv=array(
					'auth'=>1,
					'perm'=>$db->getPerm($_REQUEST['u'])
				);
		else
			$rv=array('auth'=>0);
		break;
	default:
		$rv=array('error'=>'Parameter error');
		break;
}

echo json_encode($rv);

?>