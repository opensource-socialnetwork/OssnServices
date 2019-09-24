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
$guid 	 = input('guid'); 
$photoid = input('photoid');

if(!com_is_active('OssnPhotos')){
	$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}

$delete = ossn_photos();
$delete->photoid = $photoid;

$photo = $delete->GetPhoto($delete->photoid);

$owner = ossn_albums();
$owner = $owner->GetAlbum($photo->owner_guid);

if($owner && $owner->album->owner_guid == $guid){
	if($delete->deleteAlbumPhoto()) {
		$params['OssnServices']->successResponse(array(
			'status' => true,											   
		));	
	} else {
		$params['OssnServices']->successResponse(array(
			'status' => false,											   
		));	
	}
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:cannotdelete:photo'));