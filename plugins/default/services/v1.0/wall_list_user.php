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
$guid = input('uguid');
$viewer = input('guid');

ossn_set_input('guid', $viewer); //this will late be used to check if viewer liked the post etc or nto.

$offset = input('offset', false, 1);
if($guid){
	$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnWall')){
	$wall = new OssnWall();

	$count = $wall->GetUserPosts($guid, true);
	$posts = $wall->GetUserPosts($guid);
	
	//while we have posts we need to show owner details too with each post 
	if($posts){
		foreach($posts as $key => $post){
			$posts[$key] = ossn_wallpost_to_item($post);	
			$posts[$key]['user'] = $params['OssnServices']->setUser($posts[$key]['user']);
			$posts[$key] = ossn_call_hook('services', 'wall:list:home:item', false, $posts[$key]);
		}
	}
	
	//make sure non-friend can not see the post , we simply add a flag to post that viewer is not friend
	$isFriends = null;
	if(isset($viewer) && !empty($viewer) && $user->guid !== $vuser->guid){ //make sure user not viewing own profile
				$vuser = ossn_user_by_guid($viewer);
				if($user->isFriend($user->guid, $vuser->guid)){
					$isFriends = true;
				} else {
					$isFriends = false;	
				}
	}		
	$params['OssnServices']->successResponse(array(
				'posts' => $posts,
				'count' => $count,
				'viewer_is_friend' => $isFriends,
				'user' => $params['OssnServices']->setUser($user),//show user whose we viewing the profile
				'offset' => $offset,
	));
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}