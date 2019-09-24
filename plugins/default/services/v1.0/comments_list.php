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
$comments             = new OssnComments;
$comments->page_limit = $page_limit;
$comments->limit      = input('limit', '', false);
if($type == 'entity') {
		$list  = $comments->GetComments($guid, 'entity');
		$count = $comments->countComments($guid, 'entity');
} else {
		$list  = $comments->GetComments($guid);
		$count = $comments->countComments($guid);
}
if($list) {
		$users = array();
		foreach($list as $key => $comment) {
				if(!isset($users[$comment->owner_guid])) {
						$users[$comment->owner_guid] = ossn_user_by_guid($comment->owner_guid);
				}
				if(!$user) {
						$continue;
				}
				$list[$key]->user = $params['OssnServices']->setUser($users[$comment->owner_guid]);
				if(class_exists('OssnLikes')) {
						$OssnLikes = new OssnLikes;
						$likes     = $OssnLikes->CountLikes($comment->id, 'annotation');
						if($likes) {
								$likes_total = $likes;
						} else {
								$likes_total = 0;
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
				'count' => $count,
				'comments' => $list,
				'offset' => $offset
				
		));
}