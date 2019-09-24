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
$post_guid = input('post_guid');
$guid 	   = input('guid'); //user guid
if($guid){
	$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnWall')){
	$wall = new OssnWall();

	$post = $wall->GetPost($post_guid);
	if(!$post){
		$params['OssnServices']->successResponse(true);
	}
	if($post->type == 'user'){
		if($post->owner_guid == $user->guid || $post->poster_guid == $user->guid){
				if($wall->deletePost($post_guid)){
						$params['OssnServices']->successResponse(true);
				}
		}
	}
	if($post->type == 'group'){
		$group = ossn_get_group_by_guid($post->owner_guid);
		if($group && $group->owner_guid == $user->guid || $post->poster_guid == $user->guid){
			if($wall->deletePost($post_guid)){
				$params['OssnServices']->successResponse(true);
			}
		}
	}
	$params['OssnServices']->successResponse(false);
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}