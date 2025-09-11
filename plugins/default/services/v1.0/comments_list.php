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
$guid  = input('guid'); //post guid
$uguid = input('uguid');

$offset     = input('offset', false, 1);
$page_limit = input('page_limit', '', 5);

if($page_limit && $page_limit == -1) {
		$page_limit = false;
}
$type = input('type');
if(!com_is_active('OssnComments') || !com_is_active('OssnLikes')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
if(empty($type) || empty($guid)) {
		$params['OssnServices']->throwError('106', ossn_print('ossnservices:empty:field:one:more'));
}
$comments             = new OssnComments();
$comments->page_limit = $page_limit;
$comments->limit      = input('limit', '', false);
if($type == 'entity') {
		$list  = $comments->GetComments($guid, 'entity');
		$count = $comments->countComments($guid, 'entity');
} else {
		$list  = $comments->GetComments($guid);
		$count = $comments->countComments($guid);
}
function cached_get_entity($guid) {
		static $cache = array();

		if(!isset($cache[$guid])) {
				$cache[$guid] = ossn_get_entity($guid);
		}
		return $cache[$guid];
}

function cached_get_object($guid) {
		static $cache = array();

		if(!isset($cache[$guid])) {
				$cache[$guid] = ossn_get_object($guid);
		}
		return $cache[$guid];
}

function get_business_page_cached($guid){
		static $cache = array();

		if(!isset($cache[$guid])) {
				$cache[$guid] = get_business_page($guid);
		}
		return $cache[$guid];	
}

if($list) {
		$users = array();
		foreach ($list as $key => $comment) {
				if(!isset($users[$comment->owner_guid])) {
						$users[$comment->owner_guid] = ossn_user_by_guid($comment->owner_guid);
				}
				if(!isset($users[$comment->owner_guid]) || (isset($users[$comment->owner_guid]) && !isset($users[$comment->owner_guid]->guid))) {
						continue;
				}
				if($comment->type == 'comments:entity' && com_is_active('BusinessPage')) {
						$entity = cached_get_entity($comment->subject_guid);
						if($entity && $entity->type == 'object') {
								$object = cached_get_object($entity->owner_guid);
								if($object && $object->container_type == 'businesspage') {
										$businesspage = get_business_page_cached($object->container_guid);
										if($businesspage && $businesspage->owner_guid == $comment->owner_guid) {
												$comment->businesspage = (object)array(
														'guid'       => $businesspage->guid,
														'title'      => $businesspage->title,
														'owner_guid' => $businesspage->owner_guid,
														'icon'       => $businesspage->photoURL('smaller'),
												);
										}
								}
						}
				}
				$list[$key]->user = $params['OssnServices']->setUser($users[$comment->owner_guid]);
				if(class_exists('OssnLikes')) {
						$OssnLikes = new OssnLikes();
						$likes     = $OssnLikes->CountLikes($comment->id, 'annotation');
						if($likes) {
								foreach ($OssnLikes->__likes_get_all as $item) {
										$last_three_icons[$item->subtype] = $item->subtype;
								}
								$last_three                       = array_slice($last_three_icons, -3);
								$list[$key]->last_three_reactions = $last_three;

								$likes_total = $likes;
						} else {
								$list[$key]->last_three_reactions = false;
								$likes_total                      = 0;
						}
						$list[$key]->total_likes = $likes_total;
						if($uguid && $OssnLikes->isLiked($comment->id, $uguid, 'annotation')) {
								$list[$key]->is_liked_by_user = true;
						} else {
								$list[$key]->is_liked_by_user = false;
						}
				}
		}
		$params['OssnServices']->successResponse(array(
				'count'    => $count,
				'comments' => $list,
				'offset'   => $offset,
		));
}