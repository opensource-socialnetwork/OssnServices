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
$photo_guid = input('photo_guid');
$uguid      = input('uguid');
if($photo_guid) {
		$view  = new OssnPhotos;
		$photo = $view->GetPhoto($photo_guid);
		if(isset($photo)) {
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
				$image = str_replace('album/photos/', '', $photo->value);
				$image = ossn_site_url() . "album/getphoto/{$photo->owner_guid}/{$image}";
				$list  = array(
						'guid' => $photo->guid,
						'is_liked_by_user' => $is_liked_by_user,
						'total_likes' => $likes_total,
						'image_url' => $image,
						'time_created' => $photo->time_created
				);
				$params['OssnServices']->successResponse(array(
						'album' => ossn_get_object($photo->owner_guid),
						'photo' => $list
				));
		}
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:noresponse'));
