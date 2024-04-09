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

$user  = ossn_user_by_guid($guid);
$group = ossn_get_group_by_guid($group_guid);
if(!$group) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidgroup'));
}
if($user) {
		$groupsl = array();
		$groupl = new stdClass();
		
		foreach($group as $r => $item) {
				$groupl->{$r} = $item;
		}
		$groupl->coverurl       = $group->coverURL();
		if($group->isMember($group->guid, $user->guid)){
			$params['OssnServices']->throwError('200', array(
						'status' => 'already_member',
						'error' => ossn_print('ossnservices:alreadymemebergroup'),
			));
		}
		if($group->requestExists($user->guid, $group->guid)){
			$params['OssnServices']->throwError('200', array(
						'status' => 'request_exists',
						'error' => ossn_print('ossnservices:requestexistsgroup'),
			));
		}		
		if($group->sendRequest($user->guid, $group->guid)){
			$groupl->ismember       = $group->isMember(NULL, $guid);
			$groupl->request_exists = $group->requestExists($guid, true);
			$groupl->total_requests = $group->countRequests();			
			$params['OssnServices']->successResponse(array(
					'status' => 'success',
					'group' => $groupl
			));
		}
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:nouser'));
}