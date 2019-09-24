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

$uguid = input('guid');
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
		$file->subtype    = 'profile:photo';
		$file->setFile('userphoto');
		$file->setPath('profile/photo/');
		$file->setExtension(array(
				'jpg',
				'png',
				'jpeg',
				'gif'
		));
		
		if($file->addFile()) {
				
				//update user icon time, this time has nothing to do with photo entity time
				$user->data->icon_time = time();
				$user->save();
				
				//get a all user photo files
				$resize = $file->getFiles();
				
				//add a wall post for photo update
				$profile->addPhotoWallPost($file->owner_guid, $resize->{0}->guid);
				
				if(isset($resize->{0}->value)) {
						$guid      = $user->guid;
						$datadir   = ossn_get_userdata("user/{$guid}/{$resize->{0}->value}");
						$file_name = str_replace('profile/photo/', '', $resize->{0}->value);
						
						//create sub photos
						$sizes = ossn_user_image_sizes();
						foreach($sizes as $size => $params) {
								$params  = explode('x', $params);
								$width   = $params[1];
								$height  = $params[0];
								$resized = ossn_resize_image($datadir, $width, $height, true);
								file_put_contents(ossn_get_userdata("user/{$guid}/profile/photo/{$size}_{$file_name}"), $resized);
						}
						
				}
				$OssnServices->successResponse(array(
						'guid' => $resize->{0}->guid,
						'user' => $OssnServices->setUser(ossn_user_by_guid($uguid)),
				));
		}
}
$OssnServices->throwError('200', ossn_print('ossnservices:cannotaddalbum:photo'));