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
if(!com_is_active('OssnGroups')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
} 
$guid = input('guid');
$user = ossn_user_by_guid($guid);
if($user){
	$groups = ossn_get_user_groups($user);
	if($groups){
		$groupsl = array();
		foreach($groups as $k => $group){
				foreach($group as $r => $item){
					$groupl[$k]->{$r} = $item;	
				}
				$groupl[$k]->coverurl = $group->coverURL();
		}
	}
	$params['OssnServices']->successResponse(array(
		'groups' => $groupl,											   
	));
} else {
	$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));	
}