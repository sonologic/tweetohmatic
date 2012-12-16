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