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
$post_guid = input('post_guid');
$guid 	   = input('guid'); //user guid
if($guid){
	$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnWall')){
	$wall = new OssnWall();

	$post = $wall->GetPost($post_guid);
	if(!$post || $post->subtype !== 'wall'){
		$params['OssnServices']->successResponse(false);
	}
	$post = ossn_wallpost_to_item($post);
	if($post['post']->type == 'group'){
		$group = ossn_get_group_by_guid($post['post']->owner_guid);
		if($group){
			
			$post['group']['guid']	  = $group->guid;
			$post['group']['title']	  = $group->title;
			$post['group']['ismember'] =  $group->isMember(NULL, $guid);
		}
	}
	$post['user'] = $params['OssnServices']->setUser($user);
	$post = ossn_call_hook('services', 'wall:list:home:item', false, $post);
	$params['OssnServices']->successResponse($post);
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}