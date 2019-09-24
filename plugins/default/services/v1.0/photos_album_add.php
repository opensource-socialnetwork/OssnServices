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
$album_guid  = input('album_guid');
$uguid = input('guid');
if(!com_is_active('OssnPhotos')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
if($uguid) {
		$user = ossn_user_by_guid($uguid);
}
//do a trick here as wall posts is looking for a loggedin user
$old_user = false;
if(ossn_isLoggedin()){
	$old_user = ossn_loggedin_user();
}
OssnSession::assign('OSSN_USER', $user);
$add = new OssnPhotos;
if($guid = $add->AddPhoto($album_guid, 'ossnphoto')){
	OssnSession::assign('OSSN_USER', $old_user);

	$args['photo_guids'] = array($guid);
	$args['album']       = $album_guid;
	ossn_trigger_callback('ossn:photo', 'add:multiple', $args);
	
	$params['OssnServices']->successResponse(array(
			'guid' => $guid,											   
	));
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:cannotaddalbum:photo'));