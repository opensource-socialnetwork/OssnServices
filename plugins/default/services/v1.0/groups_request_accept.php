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
if(!com_is_active('OssnGroups')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$group_guid = input('group_guid');
$guid       = input('guid');
$uguid      = input('uguid');

$user  = ossn_user_by_guid($guid);
$group = ossn_get_group_by_guid($group_guid);
if(!$group) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidgroup'));
}
if($group->owner_guid !== $uguid){
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidowner'));	
}
if($user) {
		if($group->approveRequest($user->guid, $group->guid)){		
			$params['OssnServices']->successResponse(true);
		} else {
			$params['OssnServices']->successResponse(false);			
		}
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}