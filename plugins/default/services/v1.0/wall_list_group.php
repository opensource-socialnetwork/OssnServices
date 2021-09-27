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
$group_guid = input('group_guid');
$offset = input('offset', false, 1);
if($guid){
	$user = ossn_user_by_guid($guid);
}
if(!com_is_active('OssnGroups')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$group = ossn_get_group_by_guid($group_guid);
if(!$group) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidgroup'));
}
if($user && com_is_active('OssnWall')){
	$wall = new OssnWall();

	$count = $wall->GetPostByOwner($group->guid, 'group', true);
	$posts = $wall->GetPostByOwner($group->guid, 'group');
	
	//while we have posts we need to show owner details too with each post 
	if($posts){
		foreach($posts as $key => $post){
			$posts[$key] = ossn_wallpost_to_item($post);	
			$posts[$key]['user'] = $params['OssnServices']->setUser($posts[$key]['user']);
			$posts[$key] = ossn_call_hook('services', 'wall:list:home:item', false, $posts[$key]);
		}
	}
	$params['OssnServices']->successResponse(array(
				'posts' => $posts,
				'count' => $count,
				'offset' => $offset,
				'group' => $group,
	));
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}