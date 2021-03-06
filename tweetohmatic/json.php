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

require_once('private/config.php');
require_once('inc/lib.php');
require_once('inc/db.class.php');

if(!isset($_REQUEST['c']))
	die(json_encode(array('error'=>'Parameter error')));

$db = new Db($dbpath);

switch(AUTH_METHOD) {
	case 'db':
		require_once('inc/DbAuth.class.php');
		$authenticator = new DbAuth($db);
		break;
	case 'ldap':
		require_once('inc/LdapAuth.class.php');
		$authenticator = new LdapAuth();
		break;
	default:
		throw new Exception("Invalid AUTH_METHOD.");
		break;
}

switch($_REQUEST['c']) {
	case 'ia': // is_authenticated
		$rv=array('auth'=>0);		
		if($authenticator->isAuthenticated())
			$rv=array(
					'auth'=>1,
					'perm'=>$db->getPerm($_SESSION['user'])
					);
		break;
	case 'a': // authenticate
		if($authenticator->authenticate($_REQUEST['u'],$_REQUEST['p'])) {	
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
			$rv=tweet($db,$_REQUEST['status']);
			$rv['feedback']='Status updated.';
		} else {
			$rv=queue($db,$_REQUEST['status']);
			$rv['feedback']='Status queued for moderation.';
		}
		break;
	case 'gq': // get_queue
		if($db->hasPerm($_SESSION['user'],'moderate')) {
			$rv=array('queue'=>array());
			$stmt=$db->prepare("SELECT * FROM queue ORDER BY ts DESC");
			$result=$stmt->execute();
			while($row=$result->fetchArray(SQLITE3_ASSOC)) {
				$rv['queue'][]=$row;
			}
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;
	case 'd': // delete
		if($db->hasPerm($_SESSION['user'],'moderate')) {
			$stmt=$db->prepare("DELETE FROM queue WHERE username=:user AND ts=:ts");
			$stmt->bindValue(':user',$_REQUEST['u']);
			$stmt->bindValue(':ts',$_REQUEST['ts']);
			if(!$stmt->execute()) {
				$rv=array('error'=>'failed to delete.');
			} else {
				$rv=array('error'=>'');
			}
		} else {
			$rv=array('error'=>'Access denied.');			
		}
		break;
	case 'at': // approve_tweet
		if($db->hasPerm($_SESSION['user'],'moderate')) {
			$stmt=$db->prepare("SELECT * FROM queue WHERE username=:user AND ts=:ts");
			$stmt->bindValue(':user',$_REQUEST['u']);
			$stmt->bindValue(':ts',$_REQUEST['ts']);
			$result=$stmt->execute();
			if($row=$result->fetchArray()) {
				require_once('twitter.php');
				
				$rv=tweet($db,$row['status']);
			
				$stmt=$db->prepare("DELETE FROM queue WHERE username=:user AND ts=:ts");
				$stmt->bindValue(':user',$_REQUEST['u']);
				$stmt->bindValue(':ts',$_REQUEST['ts']);
				if(!$stmt->execute()) {
					$rv=array('error'=>'Tweet sent but failed to delete from queue.');
				}
			} else {
				$rv=array('error'=>'No such tweet.');
			}
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;		
	case 'ul': // userlist
		if($db->hasPerm($_SESSION['user'],'user_admin')) {
			$users=array();
			foreach($authenticator->getUsers() as $user) {
				$users[$user]=$db->getPerm($user);
			}
			$rv=array(
					'error'=>'',
					'users'=>$users
				);
		} else {
			$rv=array('error'=>'Access denied.');
		}
		break;
	case 'su': // save_user
		if($db->hasPerm($_SESSION['user'],'user_admin')) {
			$perm=array();
			foreach(preg_split('/,/',PERMISSIONS) as $p)
				if(isset($_REQUEST[$p]) && $_REQUEST[$p])
					$perm[]=$p;
			$db->setPerm($_REQUEST['u'],$perm);
			$rv=array('error'=>'');
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