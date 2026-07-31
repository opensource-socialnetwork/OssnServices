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
$email = input('email');
$user = ossn_user_by_email($email);
if(!$user){
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}

if($user->SendResetLogin()){
	$params['OssnServices']->successResponse(array(
					'success' => true,
	));		
} else {
	$params['OssnServices']->throwError('200', ossn_print('passord:reset:fail'));
}