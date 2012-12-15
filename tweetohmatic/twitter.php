<?php

require_once('twitter/twitter.class.php');

function tweet($db,$status) {
	$twitter = new Twitter(
			$db->getValue('consumer_key',''),
			$db->getValue('consumer_key_secret',''),
			$db->getValue('access_token',''),
			$db->getValue('access_token_secret','')
	);
	
	if(!$twitter->authenticate()) {
		return array('error'=>'Invalid credentials.');
	}
	
	if(!$twitter->send($status)) {
		return array('error'=>'Update failed.');
	}
	
	return array('error'=>'');
}

function queue($db,$status) {
	$stmt=$db->prepare("INSERT INTO queue (username,ts,status) VALUES (:user,:ts,:status)");
	$stmt->bindValue(':user',$_SESSION['user']);
	$stmt->bindValue(':ts',time());
	$stmt->bindValue(':status',$status);
	if(!$stmt->execute()) {
		return array('error'=>'Queueing failed.');
	}
	return array('error'=>'');
}

?>