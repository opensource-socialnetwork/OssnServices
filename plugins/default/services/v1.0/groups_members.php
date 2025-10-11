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
$group      = ossn_get_group_by_guid($group_guid);
if(!$group) {
		$params['OssnServices']->throwError('200', ossn_print('ossnservices:invalidgroup'));
}
$ml = '';
if($group) {
		$members = $group->getMembers();
		$count   = $group->getMembers(true);
		$ml      = array();
		if($members) {
				foreach ($members as $member) {
						$ml[] = $params['OssnServices']->setUser($member);
				}
		}
		$params['OssnServices']->successResponse(array(
				'members' => $ml,
				'count'   => $count,
				'offset'  => input('offset', '', 1),
		));
} else {
		$params['OssnServices']->throwError('103', ossn_print('ossnservices:groupnomembers'));
}