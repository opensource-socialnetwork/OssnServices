<?php
/**
 * Open Source Social Network
 *
 * @package   Open Source Social Network
 * @author    Open Source Social Network Core Team <info@openteknik.com>
 * @copyright (C) OpenTeknik LLC
 * @license   Open Source Social Network License (OSSN LICENSE)  http://www.opensource-socialnetwork.org/licence
 * @link      https://www.opensource-socialnetwork.org/
 */
if(!com_is_active('OssnGroups')) {
		$params['OssnServices']->throwError('201', ossn_print('ossnservices:component:notfound'));
}
$add                   = new OssnGroup();
$params['owner_guid']  = input('owner_guid');
$params['name']        = input('groupname');
$params['description'] = input('description');
$params['privacy']     = input('privacy');
if($add->createGroup($params)) {
		$guid = $add->getGuid();
		$params['OssnServices']->successResponse(array(
				'success' => true,
				'group'   => ossn_get_group_by_guid($guid),
		));
} else {
		$params['OssnServices']->throwError('200', 'group:add:fail');
}