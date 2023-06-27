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
$photo_guid = input('photo_guid');
$uguid      = input('uguid');
if($photo_guid) {
		$view  = new OssnPhotos;
		$photo = $view->GetPhoto($photo_guid);
		//[B] Photo view shouldn't show a wrong object ID #15
		if($photo && ($photo->subtype == 'file:ossn:aphoto' || $photo->subtype == 'file:profile:photo' || $photo->subtype == 'file:profile:cover')) {
				if(class_exists('OssnLikes')) {
						$OssnLikes = new OssnLikes;
						$likes     = $OssnLikes->CountLikes($photo->guid, 'entity');
						if($likes) {
								$likes_total = $likes;
						} else {
								$likes_total = 0;
						}
						$is_liked_by_user = false;
						if($uguid && $OssnLikes->isLiked($photo->guid, $uguid, 'entity')) {
								$is_liked_by_user = true;
						}
				}
				$image = false;
				if($photo){
					$image = $photo->getURL();
				}
				$list  = array(
						'guid' => $photo->guid,
						'is_liked_by_user' => $is_liked_by_user,
						'total_likes' => $likes_total,
						'image_url' => $image,
						'time_created' => $photo->time_created
				);
				$album  = false;
				$object = ossn_get_object($photo->owner_guid);
				if($object && $object->subtype == 'ossn:album'){
						$album = $object;
				}
				$params['OssnServices']->successResponse(array(
						'album' => $album,
						'photo' => $list
				));
		}
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:noresponse'));
