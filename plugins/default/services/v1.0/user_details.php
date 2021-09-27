<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@openteknik.com>
 * @copyright © OPENTEKNIK LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$guid = input('guid');
$username = input('username');
$email  = input('email');
if($guid){
	$user = ossn_user_by_guid($guid);
}
if($username){
	$user = ossn_user_by_username($username);		
}
if($email){
	$user = ossn_user_by_email($email);		
}
if($user){
	$user = $params['OssnServices']->setUser($user);
	$params['OssnServices']->successResponse($user);
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}