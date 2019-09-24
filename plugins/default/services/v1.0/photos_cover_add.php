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

$uguid        = input('guid');
$OssnServices = $params['OssnServices'];
if(!com_is_active('OssnProfile')) {
		$OssnServices->throwError('201', ossn_print('ossnservices:component:notfound'));
}
if($uguid) {
		$user = ossn_user_by_guid($uguid);
}
if($user) {
		$profile = new OssnProfile;
		$file    = new OssnFile;
		
		$file->owner_guid = $user->guid;
		$file->type       = 'user';
		$file->subtype    = 'profile:cover';
		$file->setFile('userphoto');
		$file->setPath('profile/cover/');
		$file->setExtension(array(
				'jpg',
				'png',
				'jpeg',
				'gif'
		));
		
		if($file->addFile()) {
				
				//update user cover time, this time has nothing to do with photo entity time
				$user->data->cover_time = time();
				$user->save();
				
				$newcover = $file->getFiles();
				$profile->ResetCoverPostition($file->owner_guid);
				$profile->addPhotoWallPost($file->owner_guid, $newcover->{0}->guid, 'cover:photo');
				
				$OssnServices->successResponse(array(
						'guid' => $newcover->{0}->guid,
						'user' => $OssnServices->setUser(ossn_user_by_guid($uguid))
				));
		}
}
$OssnServices->throwError('200', ossn_print('ossnservices:cannotaddalbum:photo'));