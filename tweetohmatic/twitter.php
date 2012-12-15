<?php

require_once('twitter/twitter.class.php');

function tweet($db) {
	$twitter = new Twitter(
			$db->getValue('consumer_key',''),
			$db->getValue('consumer_key_secret',''),
			$db->getValue('access_token',''),
			$db->getValue('access_token_secret','')
	);
	
	if(!$twitter->authenticate()) {
		return array('error'=>'Invalid credentials.');
	}
	
	if(!$twitter->send($_REQUEST['status'])) {
		return array('error'=>'Update failed.');
	}
	
	return array('error'=>'');
}

function queue($db) {
	
}

?>