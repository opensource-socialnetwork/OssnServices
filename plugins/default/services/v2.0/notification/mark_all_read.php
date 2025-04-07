<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
$guid        = input('guid'); 

if(!com_is_active('OssnNotifications')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$user = ossn_user_by_guid($guid);

if(!$user) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:nouser'));
}
$notification = new OssnNotifications();
if($notification->clearAll($user->guid)){
	$params['OssnServices']->successResponse(array(
			'success' => true,
	));
} else {
	$params['OssnServices']->throwError('200', 'failed');
}
