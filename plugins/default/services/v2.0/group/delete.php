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
$uguid      = input('uguid');

$user  		= ossn_user_by_guid($uguid);
$group 		= ossn_get_group_by_guid($group_guid);

if(!$group) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidgroup'));
}
if($group->owner_guid != $uguid){
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidowner'));	
}

if ($group->deleteGroup($group->guid)) {
		$params['OssnServices']->successResponse(array(
					'status' => 'success',
					'message' => ossn_print('group:deleted'),
		));
} else {
		$params['OssnServices']->throwError('200', array(
					'status' => 'failed',
					'message' => ossn_print('group:delete:fail'),
		));	
}