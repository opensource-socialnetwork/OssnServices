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
$guid = input('guid');
$offset = input('offset', false, 1);
if($guid){
	$user = ossn_user_by_guid($guid);
}
if($user && com_is_active('OssnWall')){
	$wall = new OssnWall();
	//do a trick here as wall posts is looking for a loggedin user
	$old_user = false;
	if(ossn_isLoggedin()){
		$old_user = ossn_loggedin_user();
	}
	OssnSession::assign('OSSN_USER', $user);
	$posts = $wall->getPublicPosts(array(
				'type' => 'user',
				'distinct' => true,
	));
	$count = $wall->getPublicPosts(array(
				'type' => 'user',
				'count' => true,
				'distinct' => true,
	));
	OssnSession::assign('OSSN_USER', $old_user);
	//while we have posts we need to show owner details too with each post 
	if($posts){
		foreach($posts as $key => $post){
			$posts[$key] = ossn_wallpost_to_item($post);	
			$posts[$key]['user'] = $params['OssnServices']->setUser($posts[$key]['user']);
			if (isset($posts[$key]['image'])) {
				$posts[$key]['image'] = ossn_add_cache_to_url($posts[$key]['image']);
			}
			$posts[$key] = ossn_call_hook('services', 'wall:list:home:item', false, $posts[$key]);
		}
	}
	$params['OssnServices']->successResponse(array(
				'posts' => $posts,
				'count' => $count,
				'offset' => $offset,
	));
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}
