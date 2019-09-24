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
$owner_guid  = input('owner_guid');
$poster_guid = input('poster_guid');
$post        = input('post');
$friends     = input('friends', '', false);
$location    = input('location');
$privacy     = input('privacy');
$type		 = input('type', '', 'user');

if($owner_guid && $type !== 'group'){
	$owner = ossn_user_by_guid($owner_guid);
}
if($type == 'group'){
		$owner = ossn_get_group_by_guid($owner_guid);
}	
if($poster_guid){
	$poster = ossn_user_by_guid($poster_guid);	
}
if($poster && $owner && com_is_active('OssnWall')){
	$OssnWall = new OssnWall();
	if($type == 'group'){
			$OssnWall->type = $type;
			$friends = false;
	}
	
	$privacy = ossn_access_id_str($privacy);
	$access = "";
	if(!empty($privacy)) {
    	$access = input('privacy');
	} else {
   	 	$access = OSSN_FRIENDS;	
	}	
	if($type == 'group'){
			$access = OSSN_PRIVATE;
	}	
	$OssnWall->owner_guid  = $owner->guid;
	$OssnWall->poster_guid = $poster->guid;
	if($guid = $OssnWall->Post($post, $friends, $location, $access)){
			$post = $OssnWall->GetPost($guid);
			$post = ossn_wallpost_to_item($post);	
			$post['user'] = $params['OssnServices']->setUser($post['user']);
			
			$post = ossn_call_hook('services', 'wall:list:home:item', false, $post);
			
			$params['OssnServices']->successResponse($post);
	} else {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:wall:failed:add'));			
	}
} else {
	$params['OssnServices']->throwError('200', ossn_print('ossnservices:wall:failed:add'));	
}