<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@softlab24.com>
 * @copyright © SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$usera = input('user_a');
$userb = input('user_b');

if(!$usera || !$userb) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
if($usera){
	$usera = ossn_user_by_guid($usera);
}
if($userb){
	$userb = ossn_user_by_guid($userb);
}

if($usera && $userb){
	$response = array();
	$response['is_friend'] = false;
	$response['request_exists'] = false;
	
	if($usera->isFriend($usera->guid, $userb->guid)){
		$response['is_friend'] = true;	
	}
	if(ossn_user()->requestExists($usera->guid, $userb->guid)){
		$response['request_exists'] = true;	
	}
	$params['OssnServices']->successResponse($response);
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}