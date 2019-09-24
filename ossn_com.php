<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Social Website Core Team <info@softlab24.com>
 * @copyright SOFTLAB24 LIMITED
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
define('OssnServices', ossn_route()->com . 'OssnServices/');
ossn_register_class(array(
		'Ossn\Component\OssnServices' => OssnServices . 'classes/OssnServices.php',
		'Ossn\Component\Notifications' => OssnServices . 'classes/OssnNotifications.php'
));
/**
 * Initialize the services component  
 *
 * @return void
 */
function ossn_services_init() {
		ossn_register_com_panel('OssnServices', 'settings');
		ossn_register_page('api', 'ossn_services_handler');
		if(ossn_isAdminLoggedin()) {
				ossn_register_action('services/admin/settings', OssnServices . 'actions/settings.php');
		}
		ossn_add_hook('services', 'wall:list:home:item', 'wall_post_album_photos_services');
		ossn_add_hook('services', 'wall:list:home:item', 'wall_post_profile_cphoto_services');
		ossn_add_hook('services', 'wall:list:home:item', 'wall_post_likes_services');
		ossn_add_hook('services', 'wall:list:home:item', 'wall_post_friends_services');
}
function wall_post_friends_services($hook, $type, $return){
	if(isset($return['friends']) && !empty($return['friends'])){
			if($return['friends']){
				$services = new Ossn\Component\OssnServices;
				foreach($return['friends'] as $friend){
					$user = ossn_user_by_guid($friend);
					$return['friends'] = array();
					if($user){
						$return['friends'][] = $services->setUser($user);	
					}
				}
			}
	}
	if(isset($return['post']->poster_guid) && $return['post']->poster_guid !== $return['post']->owner_guid){
		if($return['post']->type !== 'group'){
			$user = ossn_user_by_guid($return['post']->owner_guid);
			if($user){
				$return['posted_user'] = 	$services->setUser($user);
			}
		}
	}
	return $return;
}
function wall_post_likes_services($hook, $type, $return){
			$OssnLikes = new OssnLikes;		
			$uguid = input('guid');
			if(isset($return['post']->item_type) && !empty($return['post']->item_type)){
					$OssnLikes = new OssnLikes;
					$likes = $OssnLikes->CountLikes($return['post']->item_guid, 'entity');
					if($likes){
						$return['post']->total_likes = $likes;	
					} else {
						$return['post']->total_likes = 0;	
					}		
					if($uguid && $OssnLikes->isLiked($return['post']->item_guid, $uguid, 'entity')){
						$return['post']->is_liked_by_user = true; 	
					} else {
						$return['post']->is_liked_by_user = false;	
					}
			} else {
					$likes = $OssnLikes->CountLikes($return['post']->guid);
					if($likes){
						$return['post']->total_likes = $likes;	
					} else {
						$return['post']->total_likes = 0;	
					}
					if($uguid && $OssnLikes->isLiked($return['post']->guid, $uguid, 'post')){
						$return['post']->is_liked_by_user = true; 	
					} else {
						$return['post']->is_liked_by_user = false;	
					}					
			}
			return $return;
}
function wall_post_profile_cphoto_services($hook, $type, $return){
			if(isset($return['post']->item_type) && !empty($return['post']->item_type)){
					if($return['post']->item_type == 'profile:photo'){						
						$image = ossn_get_entity( $return['post']->item_guid);
						$image = ossn_profile_photo_wall_url($image);
						$return['post']->profile_photo_url = $image;
					}
					if($return['post']->item_type == 'cover:photo'){						
						$image = ossn_get_entity( $return['post']->item_guid);
						$image = ossn_profile_coverphoto_wall_url($image);
						$return['post']->profile_cover_url = $image;
					}					
			}
			return $return;
}
function wall_post_album_photos_services($hook, $type, $return){
			if(isset($return['post']->item_type) && !empty($return['post']->item_type)){
					if($return['post']->item_type == 'album:photos:wall' && !empty($return['post']->photos_guids)){
						$photos_guid  	 = $return['post']->photos_guids;
						$photos      = ossn_get_entities(array(
								'wheres' => "e.guid IN({$photos_guid})",
								'page_limit' => 17,
						));	
						$return['post']->album_photos_wall['photos'] = $photos;
						$return['post']->album_photos_wall['album']  = ossn_get_object($return['post']->item_guid);
					}
			}
			return $return;
}
/**
 * Get the api key for the services
 *
 * @return string|boolean
 */
function ossn_services_apikey() {
		$component = new OssnComponents;
		$settings  = $component->getSettings('OssnServices');
		if(isset($settings->apikey)) {
				return $settings->apikey;
		}
		return false;
}
/**
 * Service handler
 * See the OssnServices::handle
 *
 * @return void
 */
function ossn_services_handler($requests) {
		(new \Ossn\Component\OssnServices())->handle($requests);
}

ossn_register_callback('ossn', 'init', 'ossn_services_init');