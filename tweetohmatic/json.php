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
				$rv=array(
						'auth'=>1,
						'perm'=>$db->getPerm($_SESSION['user'])
						);
		break;
	case 'a': // authenticate
		$stmt = $db->prepare("SELECT * FROM user WHERE username=:user AND password=:pass");
		$stmt->bindValue(':user',$_REQUEST['u'],SQLITE3_TEXT);
		$stmt->bindValue(':pass',hash('sha512',$_REQUEST['p']),SQLITE3_TEXT);
		$result = $stmt->execute();
		if($row=$result->fetchArray()) {
			$_SESSION['authenticated']=time();
			$_SESSION['user']=$row['username'];	
			$rv=array(
					'auth'=>1,
					'perm'=>$db->getPerm($_SESSION['user'])
				);
		} else {
			$rv=array('auth'=>0);
		}
		break;
	case 'gt': // get_twitter
		if($db->hasPerm($_SESSION['user'],'twitter_account')) {
			$rv=array('error'=>'');
			foreach(array('consumer_key','consumer_key_secret','access_token','access_token_secret') as $l) {
				$rv[$l]=$db->getValue($l,'');
			}
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;
	case 'st': // set_twitter
		if($db->hasPerm($_SESSION['user'],'twitter_account')) {
			$rv=array('error'=>'');
			foreach(array('consumer_key','consumer_key_secret','access_token','access_token_secret') as $l) {
				$db->setValue($l,$_REQUEST[$l]);
			}
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;
	case 't': // tweet
		require_once('twitter.php');
		if($db->hasPerm($_SESSION['user'],'tweet')) {
			$rv=tweet($db);
		} elseif($db->hasPerm($_SESSION['user'],'queue')) {
			$rv=queue($db);
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;
	default:
		$rv=array('error'=>'Parameter error');
		break;
}

echo json_encode($rv);

?>