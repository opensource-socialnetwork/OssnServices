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
$uguid = input('guid');
if(!com_is_active('OssnPhotos')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
if($uguid) {
		$user = ossn_user_by_guid($uguid);
}
if($user){
	$add = new OssnAlbums;
	if($add->CreateAlbum($user->guid, input('title'), input('privacy'))){
		$params['OssnServices']->successResponse(array(
			'guid' => $add->GetAlbumGuid(),											   
		));			 
	}
}
$params['OssnServices']->throwError('200', ossn_print('ossnservices:cannotaddalbum'));