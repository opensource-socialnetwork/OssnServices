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
$guid  = input('guid');
$uguid = input('uguid');
if ($guid) {
		$user = ossn_user_by_guid($guid);
}
if ($uguid) {
		$userb = ossn_user_by_guid($uguid);
}
if (!com_is_active('OssnPhotos')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));	
}
if ($user && $userb) {
		$album   = new OssnAlbums;
		$options = array();
		
		$options['owner_guid'] = $user->guid;
		$options['type']       = 'user';
		$options['subtype']    = 'ossn:album';
		$options['page_limit'] = input('page_limit', '', false);
		$photos                = $album->searchObject($options);
		
		$options['count'] = true;
		$count            = $album->searchObject($options);
		
		$albums = false;
		
		$profile       = new OssnProfile;
		$profiel_photo = $user->iconURL()->larger;
		$profile_cover = $profile->getCoverURL($user);
		
		if($photos) {
				//do a trick here as wall posts is looking for a loggedin user
				$old_user = false;
				if (ossn_isLoggedin()) {
						$old_user = ossn_loggedin_user();
				}
				OssnSession::assign('OSSN_USER', $userb);
				foreach ($photos as $photo) {
						$image = new OssnPhotos;
						$list  = $image->searchEntities(array(
								'type' => 'object',
								'owner_guid' => $photo->guid,
								'subtype' => 'file:ossn:aphoto',
								'order_by' => 'e.guid DESC',
								'limit' => 1,
								'page_limit' => 1
						));
						if($list){
							$list[0] = arrayObject($list[0], 'OssnPhotos');	
						}
						if (isset($list[0]->value)) {
								$image = $list[0]->getURL('album');
						} else {
								$image = ossn_site_url() . 'components/OssnPhotos/images/nophoto-album.png';
						}
						if (ossn_access_validate($photo->access, $photo->owner_guid)) {
								$albums[] = array(
										'image_url' => $image,
										'album' => $photo
								);
						}
				}
		}
		$params['OssnServices']->successResponse(array(
				'albums' => $albums,
				'profile_photo' => $profiel_photo,
				'cover_photo' => $profile_cover,
				'count' => $count,
				'offset' => input('offset', '', 1),
		));
	    OssnSession::assign('OSSN_USER', $old_user);		
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:usereditfailed'));