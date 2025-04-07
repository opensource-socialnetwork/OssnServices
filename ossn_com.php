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
		ossn_register_page('messages_attachment_view', 'ossn_services_messages_attachment_handler');
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
function ossn_services_message_attachment_url($message) {
		if($message && $message->isAttachment()) {
				$attachment_name = str_replace('file:', '', $message->attachment_name);
				$attachment_name = str_replace('image:', '', $attachment_name);
				//[B] OssnMessages image attachment broken if invalid file name #2339
				$path_info       = pathinfo($attachment_name);
				$attachment_name = OssnTranslit::urlize($path_info['filename']);
				return ossn_site_url("messages_attachment_view/{$message->attachment_guid}/{$attachment_name}.{$path_info['extension']}");
		}
}
function ossn_services_messages_attachment_handler($pages) {
		$file = ossn_get_file($pages[0]);
		if($file && $file->type == 'message' && $file->subtype == 'file:attachment') {
				$file->output();
		} else {
				ossn_error_page();
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
						foreach ($return['friends'] as $friend) {
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

		$item_guid = $return['post']->item_guid;
		if($return['post']->item_type == 'video' && com_is_active('Videos')) {
				$video     = ossn_get_video($return['post']->item_guid);
				$video->{'file:video'} = $video->getFileURL();
				$video->{'file:cover'} = $video->getCoverURL();
				
				$return['post']->video = $video;
				$item_guid = $video->file_guid;
		}

		if($return['post']->item_type == 'poll:item' && com_is_active('Polls')) {
				$poll     = ossn_poll_get($return['post']->item_guid);
				$item_guid = $poll->poll_entity;

				if($poll){
					$return['post']->poll = $poll;
					$return['post']->poll->html = ossn_plugin_view('polls/pages/view_main', array(
								'poll' => $poll,																			  
					));
				}
		}
		if($return['post']->item_type == 'event' && com_is_active('Events')) {
				$event_entity = ossn_get_entity($return['post']->item_guid);
				$event     = ossn_get_event($event_entity->owner_guid);
				unset($event->{'file:event:photo'});
				$event->icon_url       = $event->iconURL('master');
				$event->cl_entity_guid = $return['post']->item_guid;
				
				if($event){
					$return['post']->event = $event;
				}
		}
		if(isset($return['post']->linkPreview) && !empty($return['post']->linkPreview)) {
				$item         = ossn_get_object($return['post']->linkPreview);
				$json_default = ossn_plugin_view('linkpreview/item_inner', array(
						'item' => $item,
						'guid' => $params['post']->guid,
				));
				if(isset($item->twitter_json) || !empty($item->twitter_json)) {
						$json = json_decode($item->twitter_json, true);
						if(isset($json['html'])) {
								$html = ossn_plugin_view('linkpreview/twitter_code', array(
										'html' => $json['html'],
								));
						} else {
								$json = $json_default;
						}
				} else {
						$json = $json_default;
				}
				$return['post']->link_preview_html = $json;
		}
		$comments = new OssnComments();
		if($type == 'entity') {
				$count = $comments->countComments($item_guid, 'entity');
		} elseif($type = 'post') {
				$count = $comments->countComments($return['post']->guid);
		}
		if($count > 0) {
				$return['post']->total_comments = $count;
		} else {
				$return['post']->total_comments = 0;
		}
		return $return;
}
function ossn_services_count_last_three_reactions_total_likes_entity($guid, $uguid, $type = 'entity') {
		$return    = new stdClass();
		$OssnLikes = new OssnLikes();
		$likes     = $OssnLikes->CountLikes($guid, $type);
		if($likes) {
				foreach ($OssnLikes->__likes_get_all as $item) {
						$last_three_icons[$item->subtype] = $item->subtype;
				}
				$last_three                   = array_slice($last_three_icons, -3);
				$return->last_three_reactions = $last_three;
				$return->total_likes          = $likes;
		} else {
				$return->last_three_reactions = '';
				$return->total_likes          = 0;
		}
		if($uguid && $OssnLikes->isLiked($guid, $uguid, $type)) {
				$return->is_liked_by_user = true;
		} else {
				$return->is_liked_by_user = false;
		}
		return $return;
}
function wall_post_likes_services($hook, $type, $return) {
		$OssnLikes = new OssnLikes();
		$uguid     = input('guid');
		if(isset($return['post']->item_type) && !empty($return['post']->item_type)) {
				$item_guid = $return['post']->item_guid;
				if($return['post']->item_type == 'video' && com_is_active('Videos')) {
						$video     = ossn_get_video($return['post']->item_guid);
						$item_guid = $video->file_guid;
				}

				if($return['post']->item_type == 'poll:item' && com_is_active('Polls')) {
						$poll     = ossn_poll_get($return['post']->item_guid);
						$item_guid = $poll->poll_entity;
				}
				if($return['post']->item_type == 'event' && com_is_active('Events')) {
						$item_guid = $return['post']->item_guid;
				}		
				
				$likes  = ossn_services_count_last_three_reactions_total_likes_entity($item_guid, $uguid, 'entity');
				$return['post']->is_liked_by_user     = $likes->is_liked_by_user;
				$return['post']->last_three_reactions = $likes->last_three_reactions;
				$return['post']->total_likes          = $likes->total_likes;

		} else {
				$likes = $OssnLikes->CountLikes($return['post']->guid);
				if($likes) {
						foreach ($OssnLikes->__likes_get_all as $item) {
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
								foreach ($photos as $photo) {
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