<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    OSSN Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
define('OssnServices', ossn_route()->com . 'OssnServices/');
ossn_register_class(array(
		'Ossn\Component\OssnServices'  => OssnServices . 'classes/OssnServices.php',
		'Ossn\Component\Notifications' => OssnServices . 'classes/OssnNotifications.php',
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
		ossn_add_hook('services', 'wall:list:home:item', 'wall_post_total_comments');
		if(!ossn_isLoggedin()) {
				ossn_add_hook('private:network', 'allowed:pages', 'ossn_services_extend_prviatenetwork_pages');
		}
}
function ossn_services_extend_prviatenetwork_pages($hook, $type, $allowed_pages, $params) {
		//[B] Issue due to private network #14
		$allowed_pages[0][] = 'api';
		$allowed_pages[1][] = 'post/photo';
		$allowed_pages[1][] = 'comment/staticimage';
		$allowed_pages[1][] = 'album/getphoto';
		$allowed_pages[1][] = 'album/getcover';
		return $allowed_pages;
}
function wall_post_friends_services($hook, $type, $return) {
		if(isset($return['friends']) && !empty($return['friends'])) {
				if($return['friends']) {
						$services = new Ossn\Component\OssnServices();
						foreach($return['friends'] as $friend) {
								$user              = ossn_user_by_guid($friend);
								$return['friends'] = array();
								if($user) {
										$return['friends'][] = $services->setUser($user);
								}
						}
				}
		}
		if(isset($return['post']->poster_guid) && $return['post']->poster_guid !== $return['post']->owner_guid) {
				if($return['post']->type !== 'group') {
						$user = ossn_user_by_guid($return['post']->owner_guid);
						if($user) {
								$return['posted_user'] = $services->setUser($user);
						}
				}
		}
		return $return;
}
function wall_post_total_comments($hook, $type, $return) {
		$OssnLikes = new OssnLikes();
		$uguid     = input('guid');
		if(isset($return['post']->item_type) && !empty($return['post']->item_type)) {
				$type = 'entity';
		} else {
				$type = 'post';
		}
		$comments = new OssnComments();
		if($type == 'entity') {
				$count = $comments->countComments($return['post']->item_guid, 'entity');
		} else {
				$count = $comments->countComments($return['post']->guid);
		}
		if($count > 0) {
				$return['post']->total_comments = $count;
		} else {
				$return['post']->total_comments = 0;
		}
		return $return;
}
function wall_post_likes_services($hook, $type, $return) {
		$OssnLikes = new OssnLikes();
		$uguid     = input('guid');
		if(isset($return['post']->item_type) && !empty($return['post']->item_type)) {
				$OssnLikes = new OssnLikes();
				$likes     = $OssnLikes->CountLikes($return['post']->item_guid, 'entity');
				if($likes) {
						foreach($OssnLikes->__likes_get_all as $item) {
								$last_three_icons[$item->subtype] = $item->subtype;
						}
						$last_three                           = array_slice($last_three_icons, -3);
						$return['post']->last_three_reactions = $last_three;
						$return['post']->total_likes          = $likes;
				} else {
						$return['post']->last_three_reactions = '';
						$return['post']->total_likes          = 0;
				}
				if($uguid && $OssnLikes->isLiked($return['post']->item_guid, $uguid, 'entity')) {
						$return['post']->is_liked_by_user = true;
				} else {
						$return['post']->is_liked_by_user = false;
				}
		} else {
				$likes = $OssnLikes->CountLikes($return['post']->guid);
				if($likes) {
						foreach($OssnLikes->__likes_get_all as $item) {
								$last_three_icons[$item->subtype] = $item->subtype;
						}
						$last_three                           = array_slice($last_three_icons, -3);
						$return['post']->last_three_reactions = $last_three;
						$return['post']->total_likes          = $likes;
				} else {
						$return['post']->last_three_reactions = '';
						$return['post']->total_likes          = 0;
				}
				if($uguid && $OssnLikes->isLiked($return['post']->guid, $uguid, 'post')) {
						$return['post']->is_liked_by_user = true;
				} else {
						$return['post']->is_liked_by_user = false;
				}
		}
		return $return;
}
function wall_post_profile_cphoto_services($hook, $type, $return) {
		if(isset($return['post']->item_type) && !empty($return['post']->item_type)) {
				if($return['post']->item_type == 'profile:photo') {
						$image                             = ossn_get_file($return['post']->item_guid);
						$image                             = ossn_profile_photo_wall_url($image);
						$return['post']->profile_photo_url = $image;
				}
				if($return['post']->item_type == 'cover:photo') {
						$image                             = ossn_get_file($return['post']->item_guid);
						$image                             = ossn_profile_coverphoto_wall_url($image);
						$return['post']->profile_cover_url = $image;
				}
		}
		return $return;
}
function wall_post_album_photos_services($hook, $type, $return) {
		if(isset($return['post']->item_type) && !empty($return['post']->item_type)) {
				if($return['post']->item_type == 'album:photos:wall' && !empty($return['post']->photos_guids)) {
						$photos_guid = $return['post']->photos_guids;
						$photosObj   = new OssnPhotos();
						$photos      = $photosObj->searchFiles(array(
								'wheres'     => "e.guid IN({$photos_guid})",
								'page_limit' => 17,
						));
						$album = ossn_get_object($return['post']->item_guid);
						// < 6.1
						if($photos) {
								foreach($photos as $photo) {
										$photo->photo_url = $photo->getURL('album');
										$results[]        = $photo;
								}
						}
						$return['post']->album_photos_wall['photos'] = $photos;
						$return['post']->album_photos_wall['album']  = $album;
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
		$component = new OssnSite();
		$settings  = $component->getSettings('com:ossnservices:apikey');
		if(isset($settings) && !empty($settings)) {
				return $settings;
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
