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

$user_guid = input('guid');
$type		= input('type');
if($user_guid) {
		$list = false;
		$user 	= ossn_user_by_guid($user_guid);
		if(!$user){
			$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
		}
		if($type == 'profile' || $type == 'cover'){
			if($type == 'profile'){
				$type = 'photo';
			}	
			$files = new OssnFile;
			$list  = $files->searchEntities(array(
						'type' => 'user',
						'owner_guid' => $user_guid,
						'subtype' => "file:profile:{$type}",
						'order_by' => 'e.guid DESC',
						'page_limit' => false,
			));
			if($list){
				foreach($list as $photo) {
						$image   = str_replace('profile/photo/', '', $photo->value);
						$image   = str_replace('profile/cover/', '', $image);
						if($type == 'cover'){
							$image   = ossn_site_url() . "album/getcover/{$user->guid}/{$image}?size=larger&type=1";							
						} else {
							$image   = ossn_site_url() . "album/getphoto/{$user->guid}/{$image}?size=larger&type=1";						
						}
						$lists[] = array(
								'guid' => $photo->guid,
								'image_url' => $image
						);
				}
			}			
		}
		$params['OssnServices']->successResponse(array(
						'list' => $lists
		));			
}
$params['OssnServices']->throwError('103', ossn_print('ossnservices:noresponse'));