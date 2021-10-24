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
$guid     = input('guid'); 
$photoid  = input('photoid');
if(!com_is_active('OssnPhotos')){
	$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}

$delete = ossn_photos();
$delete->photoid = $photoid;

$photo = $delete->GetPhoto($delete->photoid);
if($photo && $photo->owner_guid == $guid){
	if($delete->deleteProfileCoverPhoto()) {
		$user                   = ossn_user_by_guid($photo->owner_guid);
		$user->data->cover_time = time();
		$user->save();
				
		$params['OssnServices']->successResponse(array(
				'status' => true,
				'user' => $params['OssnServices']->setUser($user),
		));
	} else {
		$params['OssnServices']->successResponse(array(
			'status' => false,											   
		));	
	}	
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:cannotdelete:photo'));